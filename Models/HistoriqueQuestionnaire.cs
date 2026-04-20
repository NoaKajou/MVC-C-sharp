namespace MVC_C_sharp.Models
{
    public class HistoriqueQuestionnaire
    {
        public int QuestionnaireId { get; set; }
        public string QuestionnaireNom { get; set; } = string.Empty;
        public string QuestionnaireTheme { get; set; } = string.Empty;
        public string AuteurPseudo { get; set; } = string.Empty;
        public DateTime DateConnexion { get; set; }
        public string DateConnexionAffichage => DateConnexion.ToString("dd/MM/yyyy HH:mm");
        public string Resume => $"{QuestionnaireTheme} - par {AuteurPseudo} - joue le {DateConnexionAffichage}";
    }
}
