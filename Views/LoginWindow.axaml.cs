using Avalonia.Controls;
using Avalonia.Interactivity;
using MVC_C_sharp.Controllers;

namespace MVC_C_sharp.Views
{
    public partial class LoginWindow : Window
    {
        private readonly AuthController _authController;

        public LoginWindow()
        {
            InitializeComponent();
            _authController = new AuthController();
        }

        private void BtnAction_Click(object? sender, RoutedEventArgs e)
        {
            Connexion();
        }

        private void BtnInscription_Click(object? sender, RoutedEventArgs e)
        {
            var registerWindow = new RegisterWindow();
            registerWindow.Show();
            this.Close();
        }

        private void Connexion()
        {
            var txtIdentifiant = this.FindControl<TextBox>("TxtIdentifiant");
            var txtMotDePasse = this.FindControl<TextBox>("TxtMotDePasse");
            var txtErreur = this.FindControl<TextBlock>("TxtErreur");
            var txtSucces = this.FindControl<TextBlock>("TxtSucces");

            if (txtIdentifiant == null || txtMotDePasse == null || txtErreur == null || txtSucces == null)
                return;

            txtErreur.IsVisible = false;
            txtSucces.IsVisible = false;

            string identifiant = txtIdentifiant.Text ?? "";
            string mdp = txtMotDePasse.Text ?? "";

            if (string.IsNullOrWhiteSpace(identifiant) || string.IsNullOrWhiteSpace(mdp))
            {
                txtErreur.Text = "Veuillez remplir tous les champs.";
                txtErreur.IsVisible = true;
                return;
            }

            try
            {
                var utilisateur = _authController.Connexion(identifiant, mdp);

                if (utilisateur != null)
                {
                    var mainWindow = new MainWindow(utilisateur);
                    mainWindow.Show();
                    this.Close();
                }
                else
                {
                    txtErreur.Text = "Email/Pseudo ou mot de passe incorrect.";
                    txtErreur.IsVisible = true;
                }
            }
            catch (Exception ex)
            {
                txtErreur.Text = $"Erreur BDD: {ex.Message}";
                txtErreur.IsVisible = true;
            }
        }

    }
}
