using MySql.Data.MySqlClient;
using MVC_C_sharp.Data;
using MVC_C_sharp.Models;

namespace MVC_C_sharp.Repositories
{
    public class QuestionnaireRepository
    {
        public List<Questionnaire> GetAll()
        {
            var questionnaires = new List<Questionnaire>();
            
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"SELECT q.id, q.nom, q.theme, q.utilisateur_id,
                                (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                                FROM Questionnaire q";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            questionnaires.Add(new Questionnaire
                            {
                                Id = reader.GetInt32("id"),
                                Nom = reader.GetString("nom"),
                                Theme = reader.GetString("theme"),
                                UtilisateurId = reader.GetInt32("utilisateur_id"),
                                NombreQuestions = reader.GetInt32("nb_questions")
                            });
                        }
                    }
                }
            }
            return questionnaires;
        }

        public List<Questionnaire> GetAllByUtilisateur(int utilisateurId)
        {
            var questionnaires = new List<Questionnaire>();
            
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"SELECT q.id, q.nom, q.theme, q.utilisateur_id,
                                (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                                FROM Questionnaire q WHERE q.utilisateur_id = @utilisateurId";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    
                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            questionnaires.Add(new Questionnaire
                            {
                                Id = reader.GetInt32("id"),
                                Nom = reader.GetString("nom"),
                                Theme = reader.GetString("theme"),
                                UtilisateurId = reader.GetInt32("utilisateur_id"),
                                NombreQuestions = reader.GetInt32("nb_questions")
                            });
                        }
                    }
                }
            }
            return questionnaires;
        }

        public Questionnaire? GetById(int id)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT id, nom, theme, utilisateur_id FROM Questionnaire WHERE id = @id";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    
                    using (var reader = cmd.ExecuteReader())
                    {
                        if (reader.Read())
                        {
                            return new Questionnaire
                            {
                                Id = reader.GetInt32("id"),
                                Nom = reader.GetString("nom"),
                                Theme = reader.GetString("theme"),
                                UtilisateurId = reader.GetInt32("utilisateur_id")
                            };
                        }
                    }
                }
            }
            return null;
        }

        public int Create(string nom, string theme, int utilisateurId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "INSERT INTO Questionnaire (nom, theme, utilisateur_id) VALUES (@nom, @theme, @utilisateurId); SELECT LAST_INSERT_ID();";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@nom", nom);
                    cmd.Parameters.AddWithValue("@theme", theme);
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    return Convert.ToInt32(cmd.ExecuteScalar());
                }
            }
        }

        public bool Update(int id, string nom, string theme)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "UPDATE Questionnaire SET nom = @nom, theme = @theme WHERE id = @id";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    cmd.Parameters.AddWithValue("@nom", nom);
                    cmd.Parameters.AddWithValue("@theme", theme);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }

        public bool Delete(int id)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                
                
                string deleteReponses = @"DELETE r FROM Reponse r 
                                          INNER JOIN Question q ON r.question_id = q.id 
                                          WHERE q.questionnaire_id = @id";
                using (var cmd = new MySqlCommand(deleteReponses, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    cmd.ExecuteNonQuery();
                }

               
                string deleteQuestions = "DELETE FROM Question WHERE questionnaire_id = @id";
                using (var cmd = new MySqlCommand(deleteQuestions, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    cmd.ExecuteNonQuery();
                }

                
                string deleteQuestionnaire = "DELETE FROM Questionnaire WHERE id = @id";
                using (var cmd = new MySqlCommand(deleteQuestionnaire, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }
    }
}
