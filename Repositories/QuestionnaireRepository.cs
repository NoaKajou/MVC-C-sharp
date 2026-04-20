using MySql.Data.MySqlClient;
using MVC_C_sharp.Data;
using MVC_C_sharp.Models;

namespace MVC_C_sharp.Repositories
{
    public class QuestionnaireRepository
    {
        public void EnsureFeatureSchema()
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                EnsureColumn(connection, "Questionnaire", "est_publie", "TINYINT(1) NOT NULL DEFAULT 0");
                EnsureColumn(connection, "Questionnaire", "date_publication", "DATETIME NULL DEFAULT NULL");

                string createConnexionTable = @"CREATE TABLE IF NOT EXISTS QuestionnaireConnexion (
                                                id INT NOT NULL AUTO_INCREMENT,
                                                utilisateur_id INT NOT NULL,
                                                questionnaire_id INT NOT NULL,
                                                date_connexion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                                PRIMARY KEY (id),
                                                INDEX idx_qconn_user (utilisateur_id),
                                                INDEX idx_qconn_questionnaire (questionnaire_id),
                                                INDEX idx_qconn_date (date_connexion),
                                                CONSTRAINT fk_qconn_user FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur(id) ON DELETE CASCADE ON UPDATE CASCADE,
                                                CONSTRAINT fk_qconn_questionnaire FOREIGN KEY (questionnaire_id) REFERENCES Questionnaire(id) ON DELETE CASCADE ON UPDATE CASCADE
                                            );";

                using (var cmd = new MySqlCommand(createConnexionTable, connection))
                {
                    cmd.ExecuteNonQuery();
                }
            }
        }

        private void EnsureColumn(MySqlConnection connection, string tableName, string columnName, string columnDefinition)
        {
            string query = @"SELECT COUNT(*)
                             FROM information_schema.columns
                             WHERE table_schema = DATABASE()
                               AND table_name = @tableName
                               AND column_name = @columnName";

            using (var cmd = new MySqlCommand(query, connection))
            {
                cmd.Parameters.AddWithValue("@tableName", tableName);
                cmd.Parameters.AddWithValue("@columnName", columnName);
                int count = Convert.ToInt32(cmd.ExecuteScalar());
                if (count == 0)
                {
                    string alter = $"ALTER TABLE {tableName} ADD COLUMN {columnName} {columnDefinition}";
                    using (var alterCmd = new MySqlCommand(alter, connection))
                    {
                        alterCmd.ExecuteNonQuery();
                    }
                }
            }
        }

        public List<Questionnaire> GetAll()
        {
            var questionnaires = new List<Questionnaire>();
            
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"SELECT q.id, q.nom, q.theme, q.utilisateur_id,
                                q.est_publie, q.date_publication,
                                (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                                FROM Questionnaire q
                                WHERE q.est_publie = 1";
                
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
                                NombreQuestions = reader.GetInt32("nb_questions"),
                                EstPublie = reader.GetBoolean("est_publie"),
                                DatePublication = reader.IsDBNull(reader.GetOrdinal("date_publication"))
                                    ? null
                                    : reader.GetDateTime("date_publication")
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
                                q.est_publie, q.date_publication,
                                (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                                FROM Questionnaire q
                                WHERE q.utilisateur_id = @utilisateurId
                                ORDER BY q.id DESC";
                
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
                                NombreQuestions = reader.GetInt32("nb_questions"),
                                EstPublie = reader.GetBoolean("est_publie"),
                                DatePublication = reader.IsDBNull(reader.GetOrdinal("date_publication"))
                                    ? null
                                    : reader.GetDateTime("date_publication")
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
                string query = @"SELECT q.id, q.nom, q.theme, q.utilisateur_id,
                                        q.est_publie, q.date_publication,
                                        (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                                 FROM Questionnaire q
                                 WHERE q.id = @id";
                
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
                                UtilisateurId = reader.GetInt32("utilisateur_id"),
                                NombreQuestions = reader.GetInt32("nb_questions"),
                                EstPublie = reader.GetBoolean("est_publie"),
                                DatePublication = reader.IsDBNull(reader.GetOrdinal("date_publication"))
                                    ? null
                                    : reader.GetDateTime("date_publication")
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
                string query = "INSERT INTO Questionnaire (nom, theme, utilisateur_id, est_publie, date_publication) VALUES (@nom, @theme, @utilisateurId, 0, NULL); SELECT LAST_INSERT_ID();";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@nom", nom);
                    cmd.Parameters.AddWithValue("@theme", theme);
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    return Convert.ToInt32(cmd.ExecuteScalar());
                }
            }
        }

        public bool Publish(int id, int utilisateurId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"UPDATE Questionnaire
                                 SET est_publie = 1,
                                     date_publication = IFNULL(date_publication, NOW())
                                 WHERE id = @id
                                   AND utilisateur_id = @utilisateurId";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }

        public bool TrackQuestionnaireAccess(int utilisateurId, int questionnaireId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"INSERT INTO QuestionnaireConnexion (utilisateur_id, questionnaire_id)
                                 VALUES (@utilisateurId, @questionnaireId)";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    cmd.Parameters.AddWithValue("@questionnaireId", questionnaireId);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }

        public List<HistoriqueQuestionnaire> GetPlayHistoryByUtilisateur(int utilisateurId, int limit = 25)
        {
            var history = new List<HistoriqueQuestionnaire>();

            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"SELECT q.id AS questionnaire_id,
                                        q.nom AS questionnaire_nom,
                                        q.theme AS questionnaire_theme,
                                        qc.date_connexion,
                                        u.pseudo AS auteur_pseudo
                                 FROM QuestionnaireConnexion qc
                                 INNER JOIN Questionnaire q ON q.id = qc.questionnaire_id
                                 INNER JOIN Utilisateur u ON u.id = q.utilisateur_id
                                 WHERE qc.utilisateur_id = @utilisateurId
                                 ORDER BY qc.date_connexion DESC
                                 LIMIT @limite";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    cmd.Parameters.AddWithValue("@limite", limit < 1 ? 1 : limit);

                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            history.Add(new HistoriqueQuestionnaire
                            {
                                QuestionnaireId = reader.GetInt32("questionnaire_id"),
                                QuestionnaireNom = reader.GetString("questionnaire_nom"),
                                QuestionnaireTheme = reader.GetString("questionnaire_theme"),
                                AuteurPseudo = reader.GetString("auteur_pseudo"),
                                DateConnexion = reader.GetDateTime("date_connexion")
                            });
                        }
                    }
                }
            }

            return history;
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
