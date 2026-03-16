using Avalonia.Controls;
using Avalonia.Interactivity;
using Avalonia.Markup.Xaml;
using MVC_C_sharp.Controllers;
using MVC_C_sharp.Models;

namespace MVC_C_sharp.Views
{
    public partial class AdminLogsWindow : Window
    {
        private readonly Utilisateur _utilisateur;
        private readonly AuthController _authController;

        public AdminLogsWindow() : this(new Utilisateur()) { }

        public AdminLogsWindow(Utilisateur utilisateur)
        {
            InitializeComponent();
            _utilisateur = utilisateur;
            _authController = new AuthController();

            ChargerLogs();
        }

        private void InitializeComponent()
        {
            AvaloniaXamlLoader.Load(this);
        }

        private void ChargerLogs()
        {
            var txtErreur = this.FindControl<TextBlock>("TxtErreur");
            var listLogs = this.FindControl<ListBox>("ListLogs");
            var logsContainer = this.FindControl<Border>("LogsContainer");

            if (txtErreur == null || listLogs == null || logsContainer == null)
            {
                return;
            }

            txtErreur.IsVisible = false;

            try
            {
                var logs = _authController.GetLogsAdmin(_utilisateur.Id);
                listLogs.ItemsSource = logs;
            }
            catch (UnauthorizedAccessException)
            {
                logsContainer.IsVisible = false;
                txtErreur.Text = "Acces refuse : seuls les admins peuvent voir les logs.";
                txtErreur.IsVisible = true;
            }
            catch (Exception ex)
            {
                logsContainer.IsVisible = false;
                txtErreur.Text = $"Erreur lors du chargement des logs : {ex.Message}";
                txtErreur.IsVisible = true;
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
    }
}
