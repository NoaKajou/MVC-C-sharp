namespace MVC_C_sharp.Models
{
    public class AdminLog
    {
        public int Id { get; set; }
        public int? UtilisateurId { get; set; }
        public string UtilisateurPseudo { get; set; } = string.Empty;
        public string Action { get; set; } = string.Empty;
        public string Details { get; set; } = string.Empty;
        public DateTime DateLog { get; set; }

        public string UtilisateurAffichage
        {
            get
            {
                if (!string.IsNullOrWhiteSpace(UtilisateurPseudo) && UtilisateurId.HasValue)
                {
                    return $"{UtilisateurPseudo} (#{UtilisateurId.Value})";
                }

                if (!string.IsNullOrWhiteSpace(UtilisateurPseudo))
                {
                    return UtilisateurPseudo;
                }

                if (UtilisateurId.HasValue)
                {
                    return $"User #{UtilisateurId.Value}";
                }

                return "Utilisateur anonyme";
            }
        }

        public AdminLog() { }

        public AdminLog(int id, int? utilisateurId, string action, string details, DateTime dateLog)
        {
            Id = id;
            UtilisateurId = utilisateurId;
            Action = action;
            Details = details;
            DateLog = dateLog;
        }
    }
}
