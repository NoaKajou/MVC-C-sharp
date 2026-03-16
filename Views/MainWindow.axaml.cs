using Avalonia.Controls;
using Avalonia.Interactivity;
using MVC_C_sharp.Controllers;
using MVC_C_sharp.Models;

namespace MVC_C_sharp.Views
{
    public partial class MainWindow : Window
    {
        private readonly Utilisateur _utilisateur;
        private readonly AuthController _authController;

        public MainWindow(Utilisateur utilisateur)
        {
            InitializeComponent();
            _utilisateur = utilisateur;
            _authController = new AuthController();

            var txtPseudo = this.FindControl<TextBlock>("TxtPseudo");
            if (txtPseudo != null)
            {
                txtPseudo.Text = $"{_utilisateur.Pseudo}";
            }

            var btnAdminLogs = this.FindControl<Button>("BtnAdminLogs");
            if (btnAdminLogs != null)
            {
                btnAdminLogs.IsVisible = _authController.EstAdmin(_utilisateur.Id);
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

        private void BtnAdminLogs_Click(object? sender, RoutedEventArgs e)
        {
            var adminLogsWindow = new AdminLogsWindow(_utilisateur);
            adminLogsWindow.Show();
            this.Close();
        }
    }
}
