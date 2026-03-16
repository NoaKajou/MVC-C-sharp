using Avalonia.Controls;
using Avalonia.Interactivity;
using Avalonia.Media;
using MVC_C_sharp.Controllers;
using MVC_C_sharp.Models;
using MVC_C_sharp.Repositories;
using System.Collections.ObjectModel;

namespace MVC_C_sharp.Views
{
    public partial class QuestionnairePlayWindow : Window
    {
        private readonly Questionnaire _questionnaire;
        private readonly Utilisateur _utilisateur;
        private readonly QuestionnaireController _controller;
        private readonly ReponseUtilisateurRepository _reponseRepo;
        private List<Question> _questions;
        private int _currentIndex = 0;
        private List<(int questionId, string reponse, bool? reponseBool)> _reponsesUtilisateur;
        private ObservableCollection<RadioButton> _radioButtons;

        public QuestionnairePlayWindow(Questionnaire questionnaire, Utilisateur utilisateur)
        {
            InitializeComponent();
            _questionnaire = questionnaire;
            _utilisateur = utilisateur;
            _controller = new QuestionnaireController();
            _reponseRepo = new ReponseUtilisateurRepository();
            _questions = new List<Question>();
            _reponsesUtilisateur = new List<(int, string, bool?)>();
            _radioButtons = new ObservableCollection<RadioButton>();

            Initialiser();
        }

        private void Initialiser()
        {
            var txtTitre = this.FindControl<TextBlock>("TxtTitreQuestionnaire");
            var txtTheme = this.FindControl<TextBlock>("TxtTheme");

            if (txtTitre != null)
                txtTitre.Text = _questionnaire.Nom;
            
            if (txtTheme != null)
                txtTheme.Text = $"Thème : {_questionnaire.Theme}";

            try
            {
                _questions = _controller.GetQuestions(_questionnaire.Id);
                
                if (_questions.Count == 0)
                {
                    AfficherMessage("Ce questionnaire n'a pas de questions.", false);
                    return;
                }

                AfficherQuestion();
            }
            catch (Exception ex)
            {
                AfficherMessage($"Erreur : {ex.Message}", false);
            }
        }

        private void AfficherQuestion()
        {
            if (_currentIndex >= _questions.Count)
            {
                TerminerQuestionnaire();
                return;
            }

            var question = _questions[_currentIndex];
            
            var txtQuestion = this.FindControl<TextBlock>("TxtQuestion");
            var txtProgression = this.FindControl<TextBlock>("TxtProgression");
            var panelVraiFaux = this.FindControl<StackPanel>("PanelVraiFaux");
            var panelListeValeurs = this.FindControl<StackPanel>("PanelListeValeurs");
            var btnPrecedent = this.FindControl<Button>("BtnPrecedent");
            var btnSuivant = this.FindControl<Button>("BtnSuivant");
            var rbVrai = this.FindControl<RadioButton>("RbVrai");
            var rbFaux = this.FindControl<RadioButton>("RbFaux");

            if (txtQuestion != null)
                txtQuestion.Text = $"Q{question.Numero}. {question.Libelle}";
            
            if (txtProgression != null)
                txtProgression.Text = $"Question {_currentIndex + 1} / {_questions.Count}";

            if (btnPrecedent != null)
                btnPrecedent.IsEnabled = _currentIndex > 0;
            
            if (btnSuivant != null)
                btnSuivant.Content = _currentIndex == _questions.Count - 1 ? "Terminer" : "Suivant";

            // Réinitialiser les sélections
            if (rbVrai != null) rbVrai.IsChecked = false;
            if (rbFaux != null) rbFaux.IsChecked = false;

            if (question.TypeReponse == "VraiFaux")
            {
                if (panelVraiFaux != null) panelVraiFaux.IsVisible = true;
                if (panelListeValeurs != null) panelListeValeurs.IsVisible = false;
            }
            else
            {
                if (panelVraiFaux != null) panelVraiFaux.IsVisible = false;
                if (panelListeValeurs != null) 
                {
                    panelListeValeurs.IsVisible = true;
                    ChargerReponses(question.Id);
                }
            }
        }

        private void ChargerReponses(int questionId)
        {
            var panelListeValeurs = this.FindControl<StackPanel>("PanelListeValeurs");
            if (panelListeValeurs == null) return;

            // Supprimer les anciens radio buttons (sauf le ItemsControl)
            var toRemove = panelListeValeurs.Children.OfType<RadioButton>().ToList();
            foreach (var rb in toRemove)
            {
                panelListeValeurs.Children.Remove(rb);
            }

            try
            {
                var reponses = _controller.GetReponses(questionId);
                foreach (var reponse in reponses)
                {
                    var rb = new RadioButton
                    {
                        Content = reponse.Valeur,
                        Tag = reponse,
                        GroupName = "ReponseValeur",
                        FontSize = 16,
                        Margin = new Avalonia.Thickness(0, 5)
                    };
                    panelListeValeurs.Children.Add(rb);
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine($"Erreur chargement réponses: {ex.Message}");
            }
        }

        private void BtnSuivant_Click(object? sender, RoutedEventArgs e)
        {
            if (!EnregistrerReponse())
            {
                return;
            }

            _currentIndex++;
            
            if (_currentIndex >= _questions.Count)
            {
                TerminerQuestionnaire();
            }
            else
            {
                AfficherQuestion();
            }
        }

        private void BtnPrecedent_Click(object? sender, RoutedEventArgs e)
        {
            if (_currentIndex > 0)
            {
                _currentIndex--;
                AfficherQuestion();
            }
        }

        private bool EnregistrerReponse()
        {
            var question = _questions[_currentIndex];
            string reponseTexte = "";
            bool? reponseBool = null;
            bool estCorrecte = false;

            if (question.TypeReponse == "VraiFaux")
            {
                var rbVrai = this.FindControl<RadioButton>("RbVrai");
                var rbFaux = this.FindControl<RadioButton>("RbFaux");

                if (rbVrai?.IsChecked != true && rbFaux?.IsChecked != true)
                {
                    // Pas de réponse sélectionnée
                    return false;
                }

                reponseBool = rbVrai?.IsChecked == true;
                reponseTexte = reponseBool == true ? "Vrai" : "Faux";
                estCorrecte = reponseBool == question.ReponseVraiFaux;
            }
            else
            {
                var panelListeValeurs = this.FindControl<StackPanel>("PanelListeValeurs");
                if (panelListeValeurs == null) return false;

                var selectedRb = panelListeValeurs.Children.OfType<RadioButton>()
                    .FirstOrDefault(rb => rb.IsChecked == true);

                if (selectedRb == null)
                {
                    return false;
                }

                if (selectedRb.Tag is Reponse reponse)
                {
                    reponseTexte = reponse.Valeur;
                    estCorrecte = reponse.EstCorrecte;
                }
            }

            _reponsesUtilisateur.Add((question.Id, reponseTexte, reponseBool));
            
            try
            {
                _reponseRepo.Create(_utilisateur.Id, question.Id, _questionnaire.Id, reponseTexte, reponseBool, estCorrecte);
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine($"Erreur sauvegarde réponse: {ex.Message}");
            }

            return true;
        }

        private void TerminerQuestionnaire()
        {
            var panelVraiFaux = this.FindControl<StackPanel>("PanelVraiFaux");
            var panelListeValeurs = this.FindControl<StackPanel>("PanelListeValeurs");
            var panelResultat = this.FindControl<Border>("PanelResultat");
            var txtQuestion = this.FindControl<TextBlock>("TxtQuestion");
            var txtResultat = this.FindControl<TextBlock>("TxtResultat");
            var txtScore = this.FindControl<TextBlock>("TxtScore");
            var btnSuivant = this.FindControl<Button>("BtnSuivant");
            var btnPrecedent = this.FindControl<Button>("BtnPrecedent");

            if (panelVraiFaux != null) panelVraiFaux.IsVisible = false;
            if (panelListeValeurs != null) panelListeValeurs.IsVisible = false;
            if (txtQuestion != null) txtQuestion.IsVisible = false;
            if (btnSuivant != null) btnSuivant.IsVisible = false;
            if (btnPrecedent != null) btnPrecedent.IsVisible = false;

            if (panelResultat != null)
            {
                panelResultat.IsVisible = true;
                panelResultat.Background = new SolidColorBrush(Color.Parse("#2d5a27"));
            }

            try
            {
                int score = _reponseRepo.GetScore(_utilisateur.Id, _questionnaire.Id);
                int total = _questions.Count;
                double pourcentage = (double)score / total * 100;

                try
                {
                    _controller.LogQuestionnaireCompletion(_utilisateur.Id, _questionnaire, score, total);
                }
                catch (Exception ex)
                {
                    System.Diagnostics.Debug.WriteLine($"Erreur log questionnaire: {ex.Message}");
                }

                if (txtResultat != null)
                    txtResultat.Text = "Questionnaire terminé !";
                
                if (txtScore != null)
                    txtScore.Text = $"Score : {score} / {total} ({pourcentage:F0}%)";
            }
            catch (Exception ex)
            {
                if (txtResultat != null)
                    txtResultat.Text = "Terminé";
                if (txtScore != null)
                    txtScore.Text = ex.Message;
            }
        }

        private void AfficherMessage(string message, bool isSuccess)
        {
            var panelVraiFaux = this.FindControl<StackPanel>("PanelVraiFaux");
            var panelListeValeurs = this.FindControl<StackPanel>("PanelListeValeurs");
            var panelResultat = this.FindControl<Border>("PanelResultat");
            var txtQuestion = this.FindControl<TextBlock>("TxtQuestion");
            var txtResultat = this.FindControl<TextBlock>("TxtResultat");
            var txtScore = this.FindControl<TextBlock>("TxtScore");

            if (panelVraiFaux != null) panelVraiFaux.IsVisible = false;
            if (panelListeValeurs != null) panelListeValeurs.IsVisible = false;
            if (txtQuestion != null) txtQuestion.IsVisible = false;

            if (panelResultat != null)
            {
                panelResultat.IsVisible = true;
                panelResultat.Background = new SolidColorBrush(isSuccess ? Color.Parse("#2d5a27") : Color.Parse("#8b0000"));
            }

            if (txtResultat != null)
                txtResultat.Text = message;
            
            if (txtScore != null)
                txtScore.Text = "";
        }

        private void BtnQuitter_Click(object? sender, RoutedEventArgs e)
        {
            this.Close();
        }
    }
}
