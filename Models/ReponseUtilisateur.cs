namespace MVC_C_sharp.Models
{
    public class ReponseUtilisateur
    {
        public int Id { get; set; }
        public int UtilisateurId { get; set; }
        public int QuestionId { get; set; }
        public int QuestionnaireId { get; set; }
        public string ReponseTexte { get; set; } = string.Empty;
        public bool? ReponseBool { get; set; }
        public bool EstCorrecte { get; set; }
        public DateTime DateReponse { get; set; }

        public ReponseUtilisateur() { }
    }
}
