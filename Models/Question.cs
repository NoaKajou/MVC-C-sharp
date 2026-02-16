namespace MVC_C_sharp.Models
{
    public class Question
    {
        public int Id { get; set; }
        public int QuestionnaireId { get; set; }
        public int Numero { get; set; }
        public string Libelle { get; set; } = string.Empty;
        public string TypeReponse { get; set; } = "VraiFaux";
        public bool? ReponseVraiFaux { get; set; }

        public Question() { }

        public Question(int id, int questionnaireId, int numero, string libelle, string typeReponse)
        {
            Id = id;
            QuestionnaireId = questionnaireId;
            Numero = numero;
            Libelle = libelle;
            TypeReponse = typeReponse;
        }
    }
}
