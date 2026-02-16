using Avalonia.Controls;
using Avalonia.Controls.Primitives;
using Avalonia.Interactivity;
using MVC_C_sharp.Controllers;
using MVC_C_sharp.Models;
using System.Collections.ObjectModel;

namespace MVC_C_sharp.Views
{
    public partial class QuestionEditWindow : Window
    {
        private readonly int _questionnaireId;
        private readonly Question? _question;
        private readonly QuestionnaireController _controller;
        private ObservableCollection<Reponse> _reponses;
        private int _questionId;
        private readonly bool _isNewQuestion;
        
        public Question? QuestionModifiee { get; private set; }

        public QuestionEditWindow(int questionnaireId, Question? question, bool isNew = false)
        {
            InitializeComponent();
            _questionnaireId = questionnaireId;
            _question = question;
            _isNewQuestion = isNew;
            _controller = new QuestionnaireController();
            _reponses = new ObservableCollection<Reponse>();

            InitialiserFormulaire();
        }

        private void InitialiserFormulaire()
        {
            var txtTitre = this.FindControl<TextBlock>("TxtTitreFenetre");
            var txtLibelle = this.FindControl<TextBox>("TxtLibelle");
            var cboTypeReponse = this.FindControl<ComboBox>("CboTypeReponse");
            var rbVrai = this.FindControl<RadioButton>("RbVrai");
            var rbFaux = this.FindControl<RadioButton>("RbFaux");
            var panelListeValeurs = this.FindControl<StackPanel>("PanelListeValeurs");

            if (_isNewQuestion && panelListeValeurs != null)
            {
            }

            if (_question != null && !_isNewQuestion)
            {
                _questionId = _question.Id;
                
                if (txtTitre != null)
                    txtTitre.Text = $"Modifier la question n°{_question.Numero}";
                
                if (txtLibelle != null)
                    txtLibelle.Text = _question.Libelle;
                
                if (cboTypeReponse != null)
                {
                    cboTypeReponse.SelectedIndex = _question.TypeReponse == "VraiFaux" ? 0 : 1;
                }

                if (_question.TypeReponse == "VraiFaux")
                {
                    if (rbVrai != null && rbFaux != null)
                    {
                        rbVrai.IsChecked = _question.ReponseVraiFaux == true;
                        rbFaux.IsChecked = _question.ReponseVraiFaux != true;
                    }
                }

                MettreAJourPanels();
                ChargerReponses();
            }
            else if (_question != null && _isNewQuestion)
            {
                if (txtTitre != null)
                    txtTitre.Text = "Nouvelle question";
                
                if (txtLibelle != null)
                    txtLibelle.Text = _question.Libelle;
                
                if (cboTypeReponse != null)
                    cboTypeReponse.SelectedIndex = 0;
                
                MettreAJourPanels();
            }
            else
            {
                if (txtTitre != null)
                    txtTitre.Text = "Nouvelle question";
                
                if (cboTypeReponse != null)
                    cboTypeReponse.SelectedIndex = 0;
                
                MettreAJourPanels();
            }
        }

        private void MettreAJourPanels()
        {
            var cboTypeReponse = this.FindControl<ComboBox>("CboTypeReponse");
            var panelVraiFaux = this.FindControl<StackPanel>("PanelVraiFaux");
            var panelListeValeurs = this.FindControl<StackPanel>("PanelListeValeurs");

            if (cboTypeReponse == null || panelVraiFaux == null || panelListeValeurs == null) return;

            bool isVraiFaux = cboTypeReponse.SelectedIndex == 0;
            panelVraiFaux.IsVisible = isVraiFaux;
            
            panelListeValeurs.IsVisible = !isVraiFaux && !_isNewQuestion;
        }

        private void ChargerReponses()
        {
            var gridReponses = this.FindControl<DataGrid>("GridReponses");
            if (gridReponses == null || _questionId == 0) return;

            try
            {
                var liste = _controller.GetReponses(_questionId);
                _reponses = new ObservableCollection<Reponse>(liste);
                gridReponses.ItemsSource = _reponses;
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine($"Erreur: {ex.Message}");
            }
        }

        private void CboTypeReponse_SelectionChanged(object? sender, SelectionChangedEventArgs e)
        {
            MettreAJourPanels();
        }

        private void BtnEnregistrer_Click(object? sender, RoutedEventArgs e)
        {
            var txtLibelle = this.FindControl<TextBox>("TxtLibelle");
            var cboTypeReponse = this.FindControl<ComboBox>("CboTypeReponse");
            var rbVrai = this.FindControl<RadioButton>("RbVrai");
            var txtErreur = this.FindControl<TextBlock>("TxtErreur");

            if (txtLibelle == null || cboTypeReponse == null || rbVrai == null || txtErreur == null) return;

            string libelle = txtLibelle.Text ?? "";
            string typeReponse = cboTypeReponse.SelectedIndex == 0 ? "VraiFaux" : "ListeValeurs";
            bool? reponseVraiFaux = typeReponse == "VraiFaux" ? rbVrai.IsChecked : null;

            if (string.IsNullOrWhiteSpace(libelle))
            {
                txtErreur.Text = "Veuillez saisir le libellé de la question.";
                txtErreur.IsVisible = true;
                return;
            }

            try
            {
                if (_isNewQuestion)
                {
                    QuestionModifiee = new Question
                    {
                        Id = 0,
                        QuestionnaireId = _questionnaireId,
                        Numero = _question?.Numero ?? 0,
                        Libelle = libelle,
                        TypeReponse = typeReponse,
                        ReponseVraiFaux = reponseVraiFaux
                    };
                    this.Close();
                }
                else if (_question != null && _question.Id > 0)
                {
                    _controller.UpdateQuestion(_question.Id, libelle, typeReponse, reponseVraiFaux);
                    
                    QuestionModifiee = new Question
                    {
                        Id = _question.Id,
                        QuestionnaireId = _questionnaireId,
                        Numero = _question.Numero,
                        Libelle = libelle,
                        TypeReponse = typeReponse,
                        ReponseVraiFaux = reponseVraiFaux
                    };
                    this.Close();
                }
                else
                {
                    _questionId = _controller.CreateQuestion(_questionnaireId, libelle, typeReponse, reponseVraiFaux);
                    this.Close();
                }
            }
            catch (Exception ex)
            {
                txtErreur.Text = $"Erreur: {ex.Message}";
                txtErreur.IsVisible = true;
            }
        }

        private void BtnAnnuler_Click(object? sender, RoutedEventArgs e)
        {
            QuestionModifiee = null;
            this.Close();
        }

        private void BtnAjouterValeur_Click(object? sender, RoutedEventArgs e)
        {
            if (_questionId == 0)
            {
                var txtErreur = this.FindControl<TextBlock>("TxtErreur");
                if (txtErreur != null)
                {
                    txtErreur.Text = "Veuillez d'abord enregistrer la question.";
                    txtErreur.IsVisible = true;
                }
                return;
            }

            var txtNouvelleValeur = this.FindControl<TextBox>("TxtNouvelleValeur");
            var chkEstCorrecte = this.FindControl<CheckBox>("ChkEstCorrecte");

            if (txtNouvelleValeur == null || chkEstCorrecte == null) return;

            string valeur = txtNouvelleValeur.Text ?? "";
            bool estCorrecte = chkEstCorrecte.IsChecked ?? false;

            if (string.IsNullOrWhiteSpace(valeur)) return;

            try
            {
                _controller.CreateReponse(_questionId, valeur, estCorrecte);
                txtNouvelleValeur.Text = "";
                chkEstCorrecte.IsChecked = false;
                ChargerReponses();
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine($"Erreur: {ex.Message}");
            }
        }

        private void MenuSupprimerReponse_Click(object? sender, RoutedEventArgs e)
        {
            var gridReponses = this.FindControl<DataGrid>("GridReponses");
            if (gridReponses?.SelectedItem is Reponse reponse)
            {
                try
                {
                    _controller.DeleteReponse(reponse.Id);
                    ChargerReponses();
                }
                catch (Exception ex)
                {
                    System.Diagnostics.Debug.WriteLine($"Erreur suppression: {ex.Message}");
                }
            }
        }
    }
}
