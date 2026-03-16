namespace MVC_C_sharp.Models
{
    public class Admin : Utilisateur
    {
        public DateTime DatePromotion { get; set; }

        public Admin() { }

        public Admin(int id, string email, string pseudo, string mdp, DateTime datePromotion)
            : base(id, email, pseudo, mdp)
        {
            DatePromotion = datePromotion;
        }
    }
}