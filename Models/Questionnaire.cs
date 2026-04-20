namespace MVC_C_sharp.Models
{
    public class Questionnaire
    {
        public int Id { get; set; }
        public string Nom { get; set; } = string.Empty;
        public string Theme { get; set; } = string.Empty;
        public int UtilisateurId { get; set; }
        public int NombreQuestions { get; set; }
        public bool EstPublie { get; set; }
        public DateTime? DatePublication { get; set; }
        public string StatutPublication => EstPublie ? "Publie" : "Brouillon";

        public Questionnaire() { }

        public Questionnaire(int id, string nom, string theme, int utilisateurId)
        {
            Id = id;
            Nom = nom;
            Theme = theme;
            UtilisateurId = utilisateurId;
        }
    }
}
