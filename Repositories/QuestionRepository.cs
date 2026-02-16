using MySql.Data.MySqlClient;
using MVC_C_sharp.Data;
using MVC_C_sharp.Models;

namespace MVC_C_sharp.Repositories
{
    public class QuestionRepository
    {
        public List<Question> GetAllByQuestionnaire(int questionnaireId)
        {
            var questions = new List<Question>();
            
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT id, questionnaire_id, numero, libelle, type_reponse, reponse_vrai_faux FROM Question WHERE questionnaire_id = @questionnaireId ORDER BY numero";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@questionnaireId", questionnaireId);
                    
                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            var question = new Question
                            {
                                Id = reader.GetInt32("id"),
                                QuestionnaireId = reader.GetInt32("questionnaire_id"),
                                Numero = reader.GetInt32("numero"),
                                Libelle = reader.GetString("libelle"),
                                TypeReponse = reader.GetString("type_reponse")
                            };
                            
                            if (!reader.IsDBNull(reader.GetOrdinal("reponse_vrai_faux")))
                            {
                                question.ReponseVraiFaux = reader.GetBoolean("reponse_vrai_faux");
                            }
                            
                            questions.Add(question);
                        }
                    }
                }
            }
            return questions;
        }

        public Question? GetById(int id)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT id, questionnaire_id, numero, libelle, type_reponse, reponse_vrai_faux FROM Question WHERE id = @id";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    
                    using (var reader = cmd.ExecuteReader())
                    {
                        if (reader.Read())
                        {
                            var question = new Question
                            {
                                Id = reader.GetInt32("id"),
                                QuestionnaireId = reader.GetInt32("questionnaire_id"),
                                Numero = reader.GetInt32("numero"),
                                Libelle = reader.GetString("libelle"),
                                TypeReponse = reader.GetString("type_reponse")
                            };
                            
                            if (!reader.IsDBNull(reader.GetOrdinal("reponse_vrai_faux")))
                            {
                                question.ReponseVraiFaux = reader.GetBoolean("reponse_vrai_faux");
                            }
                            
                            return question;
                        }
                    }
                }
            }
            return null;
        }

        public int GetNextNumero(int questionnaireId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT COALESCE(MAX(numero), 0) + 1 FROM Question WHERE questionnaire_id = @questionnaireId";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@questionnaireId", questionnaireId);
                    return Convert.ToInt32(cmd.ExecuteScalar());
                }
            }
        }

        public int Create(int questionnaireId, int numero, string libelle, string typeReponse, bool? reponseVraiFaux)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "INSERT INTO Question (questionnaire_id, numero, libelle, type_reponse, reponse_vrai_faux) VALUES (@questionnaireId, @numero, @libelle, @typeReponse, @reponseVraiFaux); SELECT LAST_INSERT_ID();";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@questionnaireId", questionnaireId);
                    cmd.Parameters.AddWithValue("@numero", numero);
                    cmd.Parameters.AddWithValue("@libelle", libelle);
                    cmd.Parameters.AddWithValue("@typeReponse", typeReponse);
                    cmd.Parameters.AddWithValue("@reponseVraiFaux", reponseVraiFaux.HasValue ? reponseVraiFaux.Value : DBNull.Value);
                    return Convert.ToInt32(cmd.ExecuteScalar());
                }
            }
        }

        public bool Update(int id, string libelle, string typeReponse, bool? reponseVraiFaux)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "UPDATE Question SET libelle = @libelle, type_reponse = @typeReponse, reponse_vrai_faux = @reponseVraiFaux WHERE id = @id";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    cmd.Parameters.AddWithValue("@libelle", libelle);
                    cmd.Parameters.AddWithValue("@typeReponse", typeReponse);
                    cmd.Parameters.AddWithValue("@reponseVraiFaux", reponseVraiFaux.HasValue ? reponseVraiFaux.Value : DBNull.Value);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }

        public bool Delete(int id)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                
                // Supprimer d'abord les réponses
                string deleteReponses = "DELETE FROM Reponse WHERE question_id = @id";
                using (var cmd = new MySqlCommand(deleteReponses, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    cmd.ExecuteNonQuery();
                }

                // Supprimer la question
                string deleteQuestion = "DELETE FROM Question WHERE id = @id";
                using (var cmd = new MySqlCommand(deleteQuestion, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }
    }
}
