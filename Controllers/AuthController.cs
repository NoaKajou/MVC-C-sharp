using MVC_C_sharp.Models;
using MVC_C_sharp.Repositories;

namespace MVC_C_sharp.Controllers
{
    public class AuthController
    {
        private readonly UtilisateurRepository _utilisateurRepository;

        public AuthController()
        {
            _utilisateurRepository = new UtilisateurRepository();
        }

        public Utilisateur? Connexion(string identifiant, string mdp)
        {
            var utilisateur = _utilisateurRepository.GetByEmailAndPassword(identifiant, mdp);
            
            if (utilisateur == null)
            {
                utilisateur = _utilisateurRepository.GetByPseudoAndPassword(identifiant, mdp);
            }

            return utilisateur;
        }

        public bool Inscription(string pseudo, string email, string mdp)
        {
            if (_utilisateurRepository.EmailExists(email) || _utilisateurRepository.PseudoExists(pseudo))
            {
                return false;
            }

            return _utilisateurRepository.Create(pseudo, email, mdp);
        }
    }
}
