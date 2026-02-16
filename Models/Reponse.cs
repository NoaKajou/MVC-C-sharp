namespace MVC_C_sharp.Models
{
    public class Reponse
    {
        public int Id { get; set; }
        public int QuestionId { get; set; }
        public string Valeur { get; set; } = string.Empty;
        public bool EstCorrecte { get; set; }

        public Reponse() { }

        public Reponse(int id, int questionId, string valeur, bool estCorrecte)
        {
            Id = id;
            QuestionId = questionId;
            Valeur = valeur;
            EstCorrecte = estCorrecte;
        }
    }
}
