using Avalonia.Controls;
using Avalonia.Interactivity;
using MVC_C_sharp.Models;

namespace MVC_C_sharp.Views
{
    public partial class MainWindow : Window
    {
        private readonly Utilisateur _utilisateur;
        public MainWindow(Utilisateur utilisateur)
        {
            InitializeComponent();
            _utilisateur = utilisateur;

            var txtPseudo = this.FindControl<TextBlock>("TxtPseudo");
            if (txtPseudo != null)
            {
                txtPseudo.Text = $"{_utilisateur.Pseudo}";
            }
        }

        private void BtnDeconnexion_Click(object? sender, RoutedEventArgs e)
        {
            var loginWindow = new LoginWindow();
            loginWindow.Show();
            this.Close();
        }

        private void BtnUser_Info_Click(object? sender, RoutedEventArgs e)
        {
            var userInfoWindow = new AccountInterface(_utilisateur);
            userInfoWindow.Show();
            this.Close();
        }

        private void BtnQuestionnaires_Click(object? sender, RoutedEventArgs e)
        {
            var questionnairesWindow = new QuestionnairesListWindow(_utilisateur);
            questionnairesWindow.Show();
            this.Close();
        }
    }
}
