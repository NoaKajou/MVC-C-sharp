using MVC_C_sharp.Models;
using MVC_C_sharp.Repositories;
using MVC_C_sharp.Data;

namespace MVC_C_sharp.Controllers
{
    public class AuthController
    {
        private readonly UtilisateurRepository _utilisateurRepository;
        private readonly AdminRepository _adminRepository;
        private readonly AdminLogRepository _adminLogRepository;

        public AuthController()
        {
            DatabaseInitializer.EnsureAdminAndLogTables();

            _utilisateurRepository = new UtilisateurRepository();
            _adminRepository = new AdminRepository();
            _adminLogRepository = new AdminLogRepository();
        }

        public Utilisateur? Connexion(string identifiant, string mdp)
        {
            var utilisateur = _utilisateurRepository.GetByEmailAndPassword(identifiant, mdp);
            
            if (utilisateur == null)
            {
                utilisateur = _utilisateurRepository.GetByPseudoAndPassword(identifiant, mdp);
            }

            if (utilisateur != null)
            {
                _adminLogRepository.CreateLog(
                    utilisateur.Id,
                    "CONNEXION_REUSSIE",
                    $"Connexion reussie pour l'utilisateur '{utilisateur.Pseudo}'."
                );
            }
            else
            {
                _adminLogRepository.CreateLog(
                    null,
                    "CONNEXION_ECHOUEE",
                    $"Tentative de connexion echouee pour l'identifiant '{identifiant}'."
                );
            }

            return utilisateur;
        }

        public bool Inscription(string pseudo, string email, string mdp)
        {
            if (_utilisateurRepository.EmailExists(email) || _utilisateurRepository.PseudoExists(pseudo))
            {
                _adminLogRepository.CreateLog(
                    null,
                    "INSCRIPTION_ECHOUEE",
                    $"Inscription refusee pour '{pseudo}' ({email}) : pseudo ou email deja utilise."
                );
                return false;
            }

            bool created = _utilisateurRepository.Create(pseudo, email, mdp);
            _adminLogRepository.CreateLog(
                null,
                created ? "INSCRIPTION_REUSSIE" : "INSCRIPTION_ECHOUEE",
                created
                    ? $"Inscription reussie pour '{pseudo}' ({email})."
                    : $"Echec de l'inscription pour '{pseudo}' ({email})."
            );

            return created;
        }

        public bool EstAdmin(int utilisateurId)
        {
            return _adminRepository.IsAdmin(utilisateurId);
        }

        public bool PromouvoirAdmin(int utilisateurId)
        {
            bool promoted = _adminRepository.PromoteToAdmin(utilisateurId);

            if (promoted)
            {
                _adminLogRepository.CreateLog(
                    utilisateurId,
                    "PROMOTION_ADMIN",
                    $"L'utilisateur avec l'id {utilisateurId} a ete promu admin."
                );
            }

            return promoted;
        }

        public List<AdminLog> GetLogsAdmin(int utilisateurId)
        {
            return _adminLogRepository.GetLogsForAdmin(utilisateurId);
        }
    }
}
