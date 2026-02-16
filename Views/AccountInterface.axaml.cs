using Avalonia.Controls;
using Avalonia.Interactivity;
using Avalonia.Markup.Xaml;
using MVC_C_sharp.Models;
using MVC_C_sharp.Controllers;

namespace MVC_C_sharp.Views;

public partial class AccountInterface : Window
{
    private readonly Utilisateur _utilisateur;
    private readonly QuestionnaireController _questionnaireController;
    
    public AccountInterface() : this(new Utilisateur()) { }
    
    public AccountInterface(Utilisateur utilisateur)
    {
        InitializeComponent();
        _utilisateur = utilisateur;
        _questionnaireController = new QuestionnaireController();
        
        ChargerInformationsUtilisateur();
        ChargerQuestionnaires();
    }

    private void InitializeComponent()
    {
        AvaloniaXamlLoader.Load(this);
    }

    private void ChargerInformationsUtilisateur()
    {
        // Initiale dans l'avatar
        var txtInitiale = this.FindControl<TextBlock>("TxtInitiale");
        if (txtInitiale != null && !string.IsNullOrEmpty(_utilisateur.Pseudo))
        {
            txtInitiale.Text = _utilisateur.Pseudo[0].ToString().ToUpper();
        }

        // Pseudo en en-tête
        var txtPseudo = this.FindControl<TextBlock>("TxtPseudo");
        if (txtPseudo != null)
        {
            txtPseudo.Text = _utilisateur.Pseudo;
        }

        // ID
        var txtId = this.FindControl<TextBlock>("TxtId");
        if (txtId != null)
        {
            txtId.Text = _utilisateur.Id.ToString();
        }

        // Pseudo info
        var txtPseudoInfo = this.FindControl<TextBlock>("TxtPseudoInfo");
        if (txtPseudoInfo != null)
        {
            txtPseudoInfo.Text = _utilisateur.Pseudo;
        }

        // Email
        var txtEmail = this.FindControl<TextBlock>("TxtEmail");
        if (txtEmail != null)
        {
            txtEmail.Text = _utilisateur.Email;
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

    private void ChargerQuestionnaires()
    {
        var questionnaires = _questionnaireController.GetQuestionnaires(_utilisateur.Id);
        
        var listQuestionnaires = this.FindControl<ListBox>("ListQuestionnaires");
        var txtAucunQuestionnaire = this.FindControl<TextBlock>("TxtAucunQuestionnaire");
        
        if (listQuestionnaires != null)
        {
            if (questionnaires.Count > 0)
            {
                listQuestionnaires.ItemsSource = questionnaires;
                listQuestionnaires.IsVisible = true;
                if (txtAucunQuestionnaire != null)
                    txtAucunQuestionnaire.IsVisible = false;
            }
            else
            {
                listQuestionnaires.IsVisible = false;
                if (txtAucunQuestionnaire != null)
                    txtAucunQuestionnaire.IsVisible = true;
            }
        }
    }
}
