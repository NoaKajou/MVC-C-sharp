using Avalonia.Controls;
using Avalonia.Interactivity;
using MVC_C_sharp.Controllers;

namespace MVC_C_sharp.Views
{
    public partial class RegisterWindow : Window
    {
        private readonly AuthController _authController;

        public RegisterWindow()
        {
            InitializeComponent();
            _authController = new AuthController();
        }

        private void BtnInscription_Click(object? sender, RoutedEventArgs e)
        {
            var txtPseudo = this.FindControl<TextBox>("TxtPseudo");
            var txtEmail = this.FindControl<TextBox>("TxtEmail");
            var txtMotDePasse = this.FindControl<TextBox>("TxtMotDePasse");
            var txtConfirmMotDePasse = this.FindControl<TextBox>("TxtConfirmMotDePasse");
            var txtErreur = this.FindControl<TextBlock>("TxtErreur");
            var txtSucces = this.FindControl<TextBlock>("TxtSucces");

            if (txtPseudo == null || txtEmail == null || txtMotDePasse == null || 
                txtConfirmMotDePasse == null || txtErreur == null || txtSucces == null)
                return;

            
            txtErreur.IsVisible = false;
            txtSucces.IsVisible = false;

            string pseudo = txtPseudo.Text ?? "";
            string email = txtEmail.Text ?? "";
            string mdp = txtMotDePasse.Text ?? "";
            string confirmMdp = txtConfirmMotDePasse.Text ?? "";

            
            if (string.IsNullOrWhiteSpace(pseudo) || string.IsNullOrWhiteSpace(email) || 
                string.IsNullOrWhiteSpace(mdp) || string.IsNullOrWhiteSpace(confirmMdp))
            {
                txtErreur.Text = "Veuillez remplir tous les champs.";
                txtErreur.IsVisible = true;
                return;
            }

            if (mdp != confirmMdp)
            {
                txtErreur.Text = "Les mots de passe ne correspondent pas.";
                txtErreur.IsVisible = true;
                return;
            }

            if (mdp.Length < 4)
            {
                txtErreur.Text = "Le mot de passe doit contenir au moins 4 caractères.";
                txtErreur.IsVisible = true;
                return;
            }

            try
            {
                bool success = _authController.Inscription(pseudo, email, mdp);

                if (success)
                {
                    txtSucces.Text = "Inscription réussie ! Vous pouvez vous connecter.";
                    txtSucces.IsVisible = true;
                    
                    
                    txtPseudo.Text = "";
                    txtEmail.Text = "";
                    txtMotDePasse.Text = "";
                    txtConfirmMotDePasse.Text = "";
                }
                else
                {
                    txtErreur.Text = "Cet email ou pseudo est déjà utilisé.";
                    txtErreur.IsVisible = true;
                }
            }
            catch (Exception ex)
            {
                txtErreur.Text = $"Erreur: {ex.Message}";
                txtErreur.IsVisible = true;
            }
        }

        private void BtnVersConnexion_Click(object? sender, RoutedEventArgs e)
        {
            var loginWindow = new LoginWindow();
            loginWindow.Show();
            this.Close();
        }
    }
}
