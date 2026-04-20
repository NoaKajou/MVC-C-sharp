using Avalonia.Controls;
using Avalonia.Interactivity;
using Avalonia.Markup.Xaml;
using Avalonia.Platform.Storage;
using MVC_C_sharp.Models;
using MVC_C_sharp.Controllers;
using MVC_C_sharp.Services;

namespace MVC_C_sharp.Views;

public partial class QuestionnairesListWindow : Window
{
    private readonly Utilisateur _utilisateur;
    private readonly QuestionnaireController _questionnaireController;
    private readonly PdfExportService _pdfExportService;
    
    public QuestionnairesListWindow() : this(new Utilisateur()) { }
    
    public QuestionnairesListWindow(Utilisateur utilisateur)
    {
        InitializeComponent();
        _utilisateur = utilisateur;
        _questionnaireController = new QuestionnaireController();
        _pdfExportService = new PdfExportService();
        
        ChargerQuestionnaires();
    }

    private void InitializeComponent()
    {
        AvaloniaXamlLoader.Load(this);
    }

    private void ChargerQuestionnaires()
    {
        var txtFeedback = this.FindControl<TextBlock>("TxtFeedback");
        if (txtFeedback != null)
        {
            txtFeedback.IsVisible = false;
            txtFeedback.Text = string.Empty;
        }

        // Charger tous les questionnaires
        var tousQuestionnaires = _questionnaireController.GetAllQuestionnaires();
        var listTous = this.FindControl<ListBox>("ListTousQuestionnaires");
        if (listTous != null)
        {
            listTous.ItemsSource = tousQuestionnaires;
        }

        // Charger mes questionnaires
        var mesQuestionnaires = _questionnaireController.GetQuestionnaires(_utilisateur.Id);
        var listMes = this.FindControl<ListBox>("ListMesQuestionnaires");
        var txtAucun = this.FindControl<TextBlock>("TxtAucunQuestionnaire");
        
        if (listMes != null)
        {
            if (mesQuestionnaires.Count > 0)
            {
                listMes.ItemsSource = mesQuestionnaires;
                listMes.IsVisible = true;
                if (txtAucun != null) txtAucun.IsVisible = false;
            }
            else
            {
                listMes.IsVisible = false;
                if (txtAucun != null) txtAucun.IsVisible = true;
            }
        }
    }

    private void BtnRetour_Click(object? sender, RoutedEventArgs e)
    {
        var mainWindow = new MainWindow(_utilisateur);
        mainWindow.Show();
        this.Close();
    }

    private void BtnDeconnexion_Click(object? sender, RoutedEventArgs e)
    {
        var loginWindow = new LoginWindow();
        loginWindow.Show();
        this.Close();
    }

    private void BtnJouer_Click(object? sender, RoutedEventArgs e)
    {
        var listTous = this.FindControl<ListBox>("ListTousQuestionnaires");
        if (listTous?.SelectedItem is Questionnaire questionnaire)
        {
            _questionnaireController.TrackQuestionnaireAccess(_utilisateur.Id, questionnaire.Id);
            var playWindow = new QuestionnairePlayWindow(questionnaire, _utilisateur);
            playWindow.Closed += (s, args) => ChargerQuestionnaires();
            playWindow.Show();
            this.Close();
        }
    }

    private void BtnNouveau_Click(object? sender, RoutedEventArgs e)
    {
        var editWindow = new QuestionnaireEditWindow(_utilisateur.Id, null);
        editWindow.Closed += (s, args) => ChargerQuestionnaires();
        editWindow.ShowDialog(this);
    }

    private void BtnEditer_Click(object? sender, RoutedEventArgs e)
    {
        var listMes = this.FindControl<ListBox>("ListMesQuestionnaires");
        if (listMes?.SelectedItem is Questionnaire questionnaire)
        {
            var editWindow = new QuestionnaireEditWindow(_utilisateur.Id, questionnaire);
            editWindow.Closed += (s, args) => ChargerQuestionnaires();
            editWindow.ShowDialog(this);
        }
    }

    private void BtnSupprimer_Click(object? sender, RoutedEventArgs e)
    {
        var listMes = this.FindControl<ListBox>("ListMesQuestionnaires");
        if (listMes?.SelectedItem is Questionnaire questionnaire)
        {
            _questionnaireController.DeleteQuestionnaire(questionnaire.Id, _utilisateur.Id);
            ChargerQuestionnaires();
        }
    }

    private void BtnPublier_Click(object? sender, RoutedEventArgs e)
    {
        var listMes = this.FindControl<ListBox>("ListMesQuestionnaires");
        if (listMes?.SelectedItem is not Questionnaire questionnaire)
        {
            return;
        }

        var result = _questionnaireController.PublishQuestionnaire(questionnaire.Id, _utilisateur.Id);
        AfficherFeedback(result.Message);
        ChargerQuestionnaires();
    }

    private async void BtnExporterPdf_Click(object? sender, RoutedEventArgs e)
    {
        var listMes = this.FindControl<ListBox>("ListMesQuestionnaires");
        if (listMes?.SelectedItem is not Questionnaire questionnaire)
        {
            return;
        }

        var questions = _questionnaireController.GetQuestions(questionnaire.Id);
        var reponsesByQuestion = new Dictionary<int, List<Reponse>>();
        foreach (var question in questions)
        {
            reponsesByQuestion[question.Id] = _questionnaireController.GetReponses(question.Id);
        }

        var file = await StorageProvider.SaveFilePickerAsync(new FilePickerSaveOptions
        {
            Title = "Exporter le questionnaire en PDF",
            SuggestedFileName = $"questionnaire-{questionnaire.Id}",
            FileTypeChoices = new List<FilePickerFileType>
            {
                new("PDF") { Patterns = new[] { "*.pdf" } }
            }
        });

        if (file == null)
        {
            return;
        }

        await using var stream = await file.OpenWriteAsync();
        _pdfExportService.ExportQuestionnaire(stream, questionnaire, questions, reponsesByQuestion);
        AfficherFeedback("Export PDF termine.");
    }

    private void AfficherFeedback(string message)
    {
        var txtFeedback = this.FindControl<TextBlock>("TxtFeedback");
        if (txtFeedback == null)
        {
            return;
        }

        txtFeedback.Text = message;
        txtFeedback.IsVisible = !string.IsNullOrWhiteSpace(message);
    }
}
