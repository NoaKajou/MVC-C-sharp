using MySql.Data.MySqlClient;
using MVC_C_sharp.Data;
using MVC_C_sharp.Models;

//Pense bete : ne pas supprimer c'est un fichier lien entre les reponses et les utilisateurs

namespace MVC_C_sharp.Repositories
{
    public class ReponseUtilisateurRepository
    {
        public int Create(int utilisateurId, int questionId, int questionnaireId, string reponseTexte, bool? reponseBool, bool estCorrecte)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"INSERT INTO ReponseUtilisateur 
                                (utilisateur_id, question_id, questionnaire_id, reponse_texte, reponse_bool, est_correcte, date_reponse) 
                                VALUES (@utilisateurId, @questionId, @questionnaireId, @reponseTexte, @reponseBool, @estCorrecte, @dateReponse); 
                                SELECT LAST_INSERT_ID();";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    cmd.Parameters.AddWithValue("@questionId", questionId);
                    cmd.Parameters.AddWithValue("@questionnaireId", questionnaireId);
                    cmd.Parameters.AddWithValue("@reponseTexte", reponseTexte ?? "");
                    cmd.Parameters.AddWithValue("@reponseBool", reponseBool.HasValue ? reponseBool.Value : DBNull.Value);
                    cmd.Parameters.AddWithValue("@estCorrecte", estCorrecte);
                    cmd.Parameters.AddWithValue("@dateReponse", DateTime.Now);
                    return Convert.ToInt32(cmd.ExecuteScalar());
                }
            }
        }

        public bool HasUserAnswered(int utilisateurId, int questionnaireId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT COUNT(*) FROM ReponseUtilisateur WHERE utilisateur_id = @utilisateurId AND questionnaire_id = @questionnaireId";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    cmd.Parameters.AddWithValue("@questionnaireId", questionnaireId);
                    return Convert.ToInt32(cmd.ExecuteScalar()) > 0;
                }
            }
        }

        public int GetScore(int utilisateurId, int questionnaireId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT COUNT(*) FROM ReponseUtilisateur WHERE utilisateur_id = @utilisateurId AND questionnaire_id = @questionnaireId AND est_correcte = TRUE";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    cmd.Parameters.AddWithValue("@questionnaireId", questionnaireId);
                    return Convert.ToInt32(cmd.ExecuteScalar());
                }
            }
        }

        public int GetTotalQuestions(int utilisateurId, int questionnaireId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT COUNT(*) FROM ReponseUtilisateur WHERE utilisateur_id = @utilisateurId AND questionnaire_id = @questionnaireId";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    cmd.Parameters.AddWithValue("@questionnaireId", questionnaireId);
                    return Convert.ToInt32(cmd.ExecuteScalar());
                }
            }
        }
    }
}
