namespace MVC_C_sharp.Models
{
    public class Utilisateur
    {
        public int Id { get; set; }
        public string Email { get; set; } = string.Empty;
        public string Pseudo { get; set; } = string.Empty;
        public string Mdp { get; set; } = string.Empty;

        public Utilisateur() { }

        public Utilisateur(int id, string email, string pseudo, string mdp)
        {
            Id = id;
            Email = email;
            Pseudo = pseudo;
            Mdp = mdp;
        }
    }
}
