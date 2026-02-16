using MySql.Data.MySqlClient;
using MVC_C_sharp.Data;
using MVC_C_sharp.Models;

namespace MVC_C_sharp.Repositories
{
    public class ReponseRepository
    {
        public List<Reponse> GetAllByQuestion(int questionId)
        {
            var reponses = new List<Reponse>();
            
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT id, question_id, valeur, est_correcte FROM Reponse WHERE question_id = @questionId";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@questionId", questionId);
                    
                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            reponses.Add(new Reponse
                            {
                                Id = reader.GetInt32("id"),
                                QuestionId = reader.GetInt32("question_id"),
                                Valeur = reader.GetString("valeur"),
                                EstCorrecte = reader.GetBoolean("est_correcte")
                            });
                        }
                    }
                }
            }
            return reponses;
        }

        public int Create(int questionId, string valeur, bool estCorrecte)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "INSERT INTO Reponse (question_id, valeur, est_correcte) VALUES (@questionId, @valeur, @estCorrecte); SELECT LAST_INSERT_ID();";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@questionId", questionId);
                    cmd.Parameters.AddWithValue("@valeur", valeur);
                    cmd.Parameters.AddWithValue("@estCorrecte", estCorrecte);
                    return Convert.ToInt32(cmd.ExecuteScalar());
                }
            }
        }

        public bool Update(int id, string valeur, bool estCorrecte)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "UPDATE Reponse SET valeur = @valeur, est_correcte = @estCorrecte WHERE id = @id";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    cmd.Parameters.AddWithValue("@valeur", valeur);
                    cmd.Parameters.AddWithValue("@estCorrecte", estCorrecte);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }

        public bool Delete(int id)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "DELETE FROM Reponse WHERE id = @id";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }

        public bool DeleteAllByQuestion(int questionId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "DELETE FROM Reponse WHERE question_id = @questionId";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@questionId", questionId);
                    return cmd.ExecuteNonQuery() >= 0;
                }
            }
        }
    }
}
