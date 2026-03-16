using MVC_C_sharp.Models;
using MVC_C_sharp.Data;
using MVC_C_sharp.Repositories;

namespace MVC_C_sharp.Controllers
{
    public class QuestionnaireController
    {
        private readonly QuestionnaireRepository _questionnaireRepository;
        private readonly QuestionRepository _questionRepository;
        private readonly ReponseRepository _reponseRepository;
        private readonly UtilisateurRepository _utilisateurRepository;
        private readonly AdminLogRepository _adminLogRepository;

        public QuestionnaireController()
        {
            DatabaseInitializer.EnsureAdminAndLogTables();

            _questionnaireRepository = new QuestionnaireRepository();
            _questionRepository = new QuestionRepository();
            _reponseRepository = new ReponseRepository();
            _utilisateurRepository = new UtilisateurRepository();
            _adminLogRepository = new AdminLogRepository();
        }

        public List<Questionnaire> GetAllQuestionnaires()
        {
            return _questionnaireRepository.GetAll();
        }

        public List<Questionnaire> GetQuestionnaires(int utilisateurId)
        {
            return _questionnaireRepository.GetAllByUtilisateur(utilisateurId);
        }

        public Questionnaire? GetQuestionnaire(int id)
        {
            return _questionnaireRepository.GetById(id);
        }

        public int CreateQuestionnaire(string nom, string theme, int utilisateurId)
        {
            int questionnaireId = _questionnaireRepository.Create(nom, theme, utilisateurId);

            if (questionnaireId > 0)
            {
                string pseudo = GetUtilisateurPseudo(utilisateurId);
                _adminLogRepository.CreateLog(
                    utilisateurId,
                    "QUESTIONNAIRE_CREE",
                    $"Le questionnaire '{nom}' (theme : {theme}) a ete cree par '{pseudo}'."
                );
            }

            return questionnaireId;
        }

        public bool UpdateQuestionnaire(int id, string nom, string theme)
        {
            return _questionnaireRepository.Update(id, nom, theme);
        }

        public bool DeleteQuestionnaire(int id, int utilisateurId)
        {
            var questionnaire = _questionnaireRepository.GetById(id);
            bool deleted = _questionnaireRepository.Delete(id);

            if (deleted)
            {
                string pseudo = GetUtilisateurPseudo(utilisateurId);
                string nom = questionnaire?.Nom ?? $"Questionnaire #{id}";
                string theme = questionnaire?.Theme ?? "Inconnu";

                _adminLogRepository.CreateLog(
                    utilisateurId,
                    "QUESTIONNAIRE_SUPPRIME",
                    $"Le questionnaire '{nom}' (theme : {theme}) a ete supprime par '{pseudo}'."
                );
            }

            return deleted;
        }

        public void LogQuestionnaireCompletion(int utilisateurId, Questionnaire questionnaire, int score, int total)
        {
            string pseudo = GetUtilisateurPseudo(utilisateurId);
            double pourcentage = total > 0 ? (double)score / total * 100 : 0;

            _adminLogRepository.CreateLog(
                utilisateurId,
                "QUESTIONNAIRE_TERMINE",
                $"Le questionnaire '{questionnaire.Nom}' (theme : {questionnaire.Theme}) a ete termine par '{pseudo}' avec un score de {score}/{total} ({pourcentage:F0}%)."
            );
        }

        public List<Question> GetQuestions(int questionnaireId)
        {
            return _questionRepository.GetAllByQuestionnaire(questionnaireId);
        }

        public Question? GetQuestion(int id)
        {
            return _questionRepository.GetById(id);
        }

        public int CreateQuestion(int questionnaireId, string libelle, string typeReponse, bool? reponseVraiFaux)
        {
            int numero = _questionRepository.GetNextNumero(questionnaireId);
            return _questionRepository.Create(questionnaireId, numero, libelle, typeReponse, reponseVraiFaux);
        }

        public bool UpdateQuestion(int id, string libelle, string typeReponse, bool? reponseVraiFaux)
        {
            return _questionRepository.Update(id, libelle, typeReponse, reponseVraiFaux);
        }

        public bool DeleteQuestion(int id)
        {
            return _questionRepository.Delete(id);
        }

        public List<Reponse> GetReponses(int questionId)
        {
            return _reponseRepository.GetAllByQuestion(questionId);
        }

        public int CreateReponse(int questionId, string valeur, bool estCorrecte)
        {
            return _reponseRepository.Create(questionId, valeur, estCorrecte);
        }

        public bool UpdateReponse(int id, string valeur, bool estCorrecte)
        {
            return _reponseRepository.Update(id, valeur, estCorrecte);
        }

        public bool DeleteReponse(int id)
        {
            return _reponseRepository.Delete(id);
        }

        public bool DeleteAllReponses(int questionId)
        {
            return _reponseRepository.DeleteAllByQuestion(questionId);
        }

        private string GetUtilisateurPseudo(int utilisateurId)
        {
            var utilisateur = _utilisateurRepository.GetById(utilisateurId);
            return utilisateur?.Pseudo ?? $"User #{utilisateurId}";
        }
    }
}
