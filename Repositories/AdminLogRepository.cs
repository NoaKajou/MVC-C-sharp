using MySql.Data.MySqlClient;
using MVC_C_sharp.Data;
using MVC_C_sharp.Models;

namespace MVC_C_sharp.Repositories
{
    public class AdminLogRepository
    {
        private readonly AdminRepository _adminRepository;

        public AdminLogRepository()
        {
            _adminRepository = new AdminRepository();
        }

        public void EnsureLogTable()
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"CREATE TABLE IF NOT EXISTS LogAdmin (
                                    id INT NOT NULL AUTO_INCREMENT,
                                    utilisateur_id INT NULL,
                                    action VARCHAR(100) NOT NULL,
                                    details TEXT NOT NULL,
                                    date_log DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (id),
                                    INDEX idx_log_admin_date (date_log),
                                    INDEX idx_log_admin_user (utilisateur_id),
                                    CONSTRAINT fk_log_admin_utilisateur
                                        FOREIGN KEY (utilisateur_id)
                                        REFERENCES Utilisateur(id)
                                        ON DELETE SET NULL
                                );";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.ExecuteNonQuery();
                }
            }
        }

        public void CreateLog(int? utilisateurId, string action, string details)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"INSERT INTO LogAdmin (utilisateur_id, action, details, date_log)
                                 VALUES (@utilisateurId, @action, @details, @dateLog)";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@utilisateurId", utilisateurId.HasValue ? utilisateurId.Value : DBNull.Value);
                    cmd.Parameters.AddWithValue("@action", action);
                    cmd.Parameters.AddWithValue("@details", details);
                    cmd.Parameters.AddWithValue("@dateLog", DateTime.Now);
                    cmd.ExecuteNonQuery();
                }
            }
        }

        public List<AdminLog> GetLogsForAdmin(int utilisateurId)
        {
            if (!_adminRepository.IsAdmin(utilisateurId))
            {
                throw new UnauthorizedAccessException("Acces refuse : seuls les admins peuvent consulter les logs.");
            }

            return GetAllLogs();
        }

        private List<AdminLog> GetAllLogs()
        {
            var logs = new List<AdminLog>();

            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = @"SELECT l.id, l.utilisateur_id, l.action, l.details, l.date_log, u.pseudo
                                 FROM LogAdmin l
                                 LEFT JOIN Utilisateur u ON u.id = l.utilisateur_id
                                 ORDER BY l.date_log DESC";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            logs.Add(new AdminLog
                            {
                                Id = reader.GetInt32("id"),
                                UtilisateurId = reader.IsDBNull(reader.GetOrdinal("utilisateur_id"))
                                    ? null
                                    : reader.GetInt32("utilisateur_id"),
                                UtilisateurPseudo = reader.IsDBNull(reader.GetOrdinal("pseudo"))
                                    ? string.Empty
                                    : reader.GetString("pseudo"),
                                Action = reader.GetString("action"),
                                Details = reader.GetString("details"),
                                DateLog = reader.GetDateTime("date_log")
                            });
                        }
                    }
                }
            }

            return logs;
        }
    }
}
