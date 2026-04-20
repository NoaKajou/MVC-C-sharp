using Avalonia.Controls;
using Avalonia.Controls.Primitives;
using Avalonia.Interactivity;
using Avalonia.Platform.Storage;
using MVC_C_sharp.Controllers;
using MVC_C_sharp.Models;
using MVC_C_sharp.Services;
using System.Collections.ObjectModel;

namespace MVC_C_sharp.Views
{
    public partial class QuestionnaireEditWindow : Window
    {
        private readonly int _utilisateurId;
        private readonly Questionnaire? _questionnaire;
        private readonly QuestionnaireController _controller;
        private readonly PdfExportService _pdfExportService;
        private ObservableCollection<Question> _questions;
        private int _questionnaireId;
        private int _numeroQuestion = 1;

        public QuestionnaireEditWindow(int utilisateurId, Questionnaire? questionnaire)
        {
            InitializeComponent();
            _utilisateurId = utilisateurId;
            _questionnaire = questionnaire;
            _controller = new QuestionnaireController();
            _pdfExportService = new PdfExportService();
            _questions = new ObservableCollection<Question>();

            InitialiserFormulaire();
        }

        private void InitialiserFormulaire()
        {
            var txtTitre = this.FindControl<TextBlock>("TxtTitreFenetre");
            var txtNom = this.FindControl<TextBox>("TxtNom");
            var cboTheme = this.FindControl<ComboBox>("CboTheme");
            var gridQuestions = this.FindControl<DataGrid>("GridQuestions");

            if (gridQuestions != null)
            {
                gridQuestions.ItemsSource = _questions;
            }

            if (_questionnaire != null)
            {
                _questionnaireId = _questionnaire.Id;
                
                if (txtTitre != null)
                    txtTitre.Text = "Modifier le questionnaire";
                
                if (txtNom != null)
                    txtNom.Text = _questionnaire.Nom;
                
                if (cboTheme != null)
                {
                    for (int i = 0; i < cboTheme.Items.Count; i++)
                    {
                        if (cboTheme.Items[i] is ComboBoxItem item && 
                            item.Content?.ToString() == _questionnaire.Theme)
                        {
                            cboTheme.SelectedIndex = i;
                            break;
                        }
                    }
                }

                ChargerQuestions();

                var btnPublier = this.FindControl<Button>("BtnPublier");
                var btnExporterPdf = this.FindControl<Button>("BtnExporterPdf");
                if (btnPublier != null)
                {
                    btnPublier.IsVisible = !_questionnaire.EstPublie;
                }
                if (btnExporterPdf != null)
                {
                    btnExporterPdf.IsVisible = true;
                }
            }
            else
            {
                if (txtTitre != null)
                    txtTitre.Text = "Nouveau questionnaire";
                
                if (cboTheme != null)
                    cboTheme.SelectedIndex = 0;

                var btnPublier = this.FindControl<Button>("BtnPublier");
                var btnExporterPdf = this.FindControl<Button>("BtnExporterPdf");
                if (btnPublier != null)
                {
                    btnPublier.IsVisible = false;
                }
                if (btnExporterPdf != null)
                {
                    btnExporterPdf.IsVisible = false;
                }
            }
        }

        private void ChargerQuestions()
        {
            if (_questionnaireId == 0) return;

            try
            {
                var liste = _controller.GetQuestions(_questionnaireId);
                _questions.Clear();
                foreach (var q in liste)
                {
                    _questions.Add(q);
                }
                _numeroQuestion = _questions.Count + 1;
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine($"Erreur: {ex.Message}");
            }
        }

        private void MettreAJourGrid()
        {
            var gridQuestions = this.FindControl<DataGrid>("GridQuestions");
            if (gridQuestions != null)
            {
                gridQuestions.ItemsSource = null;
                gridQuestions.ItemsSource = _questions;
            }
        }

        private void BtnEnregistrer_Click(object? sender, RoutedEventArgs e)
        {
            var txtNom = this.FindControl<TextBox>("TxtNom");
            var cboTheme = this.FindControl<ComboBox>("CboTheme");
            var txtErreur = this.FindControl<TextBlock>("TxtErreur");

            if (txtNom == null || cboTheme == null || txtErreur == null) return;

            string nom = txtNom.Text ?? "";
            string theme = (cboTheme.SelectedItem as ComboBoxItem)?.Content?.ToString() ?? "";

            if (string.IsNullOrWhiteSpace(nom))
            {
                txtErreur.Text = "Veuillez saisir un nom pour le questionnaire.";
                txtErreur.IsVisible = true;
                return;
            }

            try
            {
                if (_questionnaire != null)
                {
                    _controller.UpdateQuestionnaire(_questionnaire.Id, nom, theme);
                    
                    foreach (var question in _questions.Where(q => q.Id == 0))
                    {
                        _controller.CreateQuestion(_questionnaireId, question.Libelle, question.TypeReponse, question.ReponseVraiFaux);
                    }
                }
                else
                {
                    _questionnaireId = _controller.CreateQuestionnaire(nom, theme, _utilisateurId);
                    
                    foreach (var question in _questions)
                    {
                        _controller.CreateQuestion(_questionnaireId, question.Libelle, question.TypeReponse, question.ReponseVraiFaux);
                    }
                }

                txtErreur.IsVisible = false;
                this.Close();
            }
            catch (Exception ex)
            {
                txtErreur.Text = $"Erreur: {ex.Message}";
                txtErreur.IsVisible = true;
            }
        }

        private void BtnAnnuler_Click(object? sender, RoutedEventArgs e)
        {
            this.Close();
        }

        private void BtnPublier_Click(object? sender, RoutedEventArgs e)
        {
            var txtErreur = this.FindControl<TextBlock>("TxtErreur");
            if (_questionnaire == null || _questionnaireId == 0)
            {
                if (txtErreur != null)
                {
                    txtErreur.Text = "Enregistrez d'abord le questionnaire avant publication.";
                    txtErreur.IsVisible = true;
                }
                return;
            }

            var result = _controller.PublishQuestionnaire(_questionnaireId, _utilisateurId);
            if (txtErreur != null)
            {
                txtErreur.Text = result.Message;
                txtErreur.IsVisible = true;
            }

            if (result.Success)
            {
                _questionnaire.EstPublie = true;
                var btnPublier = this.FindControl<Button>("BtnPublier");
                if (btnPublier != null)
                {
                    btnPublier.IsVisible = false;
                }
            }
        }

        private async void BtnExporterPdf_Click(object? sender, RoutedEventArgs e)
        {
            var txtErreur = this.FindControl<TextBlock>("TxtErreur");
            if (_questionnaire == null || _questionnaireId == 0)
            {
                if (txtErreur != null)
                {
                    txtErreur.Text = "Enregistrez d'abord le questionnaire avant export PDF.";
                    txtErreur.IsVisible = true;
                }
                return;
            }

            var questions = _controller.GetQuestions(_questionnaireId);
            var reponsesByQuestion = new Dictionary<int, List<Reponse>>();
            foreach (var question in questions)
            {
                reponsesByQuestion[question.Id] = _controller.GetReponses(question.Id);
            }

            var file = await StorageProvider.SaveFilePickerAsync(new FilePickerSaveOptions
            {
                Title = "Exporter le questionnaire en PDF",
                SuggestedFileName = $"questionnaire-{_questionnaireId}",
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
            _pdfExportService.ExportQuestionnaire(stream, _questionnaire, questions, reponsesByQuestion);

            if (txtErreur != null)
            {
                txtErreur.Text = "Export PDF termine.";
                txtErreur.IsVisible = true;
            }
        }

        private async void MenuAjouterQuestion_Click(object? sender, RoutedEventArgs e)
        {
            var nouvelleQuestion = new Question
            {
                Id = 0,
                QuestionnaireId = _questionnaireId,
                Numero = _numeroQuestion,
                Libelle = "",
                TypeReponse = "VraiFaux",
                ReponseVraiFaux = false
            };

            var editWindow = new QuestionEditWindow(_questionnaireId, nouvelleQuestion, true);
            await editWindow.ShowDialog(this);
            
            if (editWindow.QuestionModifiee != null && !string.IsNullOrWhiteSpace(editWindow.QuestionModifiee.Libelle))
            {
                editWindow.QuestionModifiee.Numero = _numeroQuestion;
                _questions.Add(editWindow.QuestionModifiee);
                _numeroQuestion++;
                MettreAJourGrid();
            }
        }

        private async void MenuEditerQuestion_Click(object? sender, RoutedEventArgs e)
        {
            var gridQuestions = this.FindControl<DataGrid>("GridQuestions");
            if (gridQuestions?.SelectedItem is Question question)
            {
                bool isNew = question.Id == 0;
                var editWindow = new QuestionEditWindow(_questionnaireId, question, isNew);
                await editWindow.ShowDialog(this);
                
                if (editWindow.QuestionModifiee != null)
                {
                    int index = _questions.IndexOf(question);
                    if (index >= 0)
                    {
                        _questions[index] = editWindow.QuestionModifiee;
                        MettreAJourGrid();
                    }
                }
            }
        }

        private void MenuSupprimerQuestion_Click(object? sender, RoutedEventArgs e)
        {
            var gridQuestions = this.FindControl<DataGrid>("GridQuestions");
            if (gridQuestions?.SelectedItem is Question question)
            {
                try
                {
                    if (question.Id > 0)
                    {
                        _controller.DeleteQuestion(question.Id);
                    }
                    
                    _questions.Remove(question);
                    
                    int num = 1;
                    foreach (var q in _questions)
                    {
                        q.Numero = num++;
                    }
                    _numeroQuestion = num;
                    MettreAJourGrid();
                }
                catch (Exception ex)
                {
                    System.Diagnostics.Debug.WriteLine($"Erreur suppression: {ex.Message}");
                }
            }
        }
    }
}
