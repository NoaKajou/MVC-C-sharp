using MVC_C_sharp.Models;
using MVC_C_sharp.Repositories;

namespace MVC_C_sharp.Controllers
{
    public class QuestionnaireController
    {
        private readonly QuestionnaireRepository _questionnaireRepository;
        private readonly QuestionRepository _questionRepository;
        private readonly ReponseRepository _reponseRepository;

        public QuestionnaireController()
        {
            _questionnaireRepository = new QuestionnaireRepository();
            _questionRepository = new QuestionRepository();
            _reponseRepository = new ReponseRepository();
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
            return _questionnaireRepository.Create(nom, theme, utilisateurId);
        }

        public bool UpdateQuestionnaire(int id, string nom, string theme)
        {
            return _questionnaireRepository.Update(id, nom, theme);
        }

        public bool DeleteQuestionnaire(int id)
        {
            return _questionnaireRepository.Delete(id);
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
    }
}
