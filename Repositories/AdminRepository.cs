using MySql.Data.MySqlClient;
using MVC_C_sharp.Data;
using MVC_C_sharp.Models;

namespace MVC_C_sharp.Repositories
{
    public class AdminRepository
    {
        public void EnsureAdminTable()
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"CREATE TABLE IF NOT EXISTS Admin (
                                    utilisateur_id INT NOT NULL,
                                    date_promotion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (utilisateur_id),
                                    CONSTRAINT fk_admin_utilisateur
                                        FOREIGN KEY (utilisateur_id)
                                        REFERENCES Utilisateur(id)
                                        ON DELETE CASCADE
                                );";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.ExecuteNonQuery();
                }
            }
        }

        public bool IsAdmin(int utilisateurId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT COUNT(*) FROM Admin WHERE utilisateur_id = @utilisateurId";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    return Convert.ToInt32(cmd.ExecuteScalar()) > 0;
                }
            }
        }

        public Admin? GetByUtilisateurId(int utilisateurId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"SELECT u.id, u.email, u.pseudo, u.mdp, a.date_promotion
                                 FROM Admin a
                                 INNER JOIN Utilisateur u ON u.id = a.utilisateur_id
                                 WHERE a.utilisateur_id = @utilisateurId";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);

                    using (var reader = cmd.ExecuteReader())
                    {
                        if (reader.Read())
                        {
                            return new Admin
                            {
                                Id = reader.GetInt32("id"),
                                Email = reader.GetString("email"),
                                Pseudo = reader.GetString("pseudo"),
                                Mdp = reader.GetString("mdp"),
                                DatePromotion = reader.GetDateTime("date_promotion")
                            };
                        }
                    }
                }
            }

            return null;
        }

        public bool PromoteToAdmin(int utilisateurId)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "INSERT IGNORE INTO Admin (utilisateur_id) VALUES (@utilisateurId)";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }
    }
}
