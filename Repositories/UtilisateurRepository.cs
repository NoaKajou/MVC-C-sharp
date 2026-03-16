using MySql.Data.MySqlClient;
using MVC_C_sharp.Data;
using MVC_C_sharp.Models;

namespace MVC_C_sharp.Repositories
{
    public class UtilisateurRepository
    {
        public Utilisateur? GetById(int id)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT id, email, pseudo, mdp FROM Utilisateur WHERE id = @id";

                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@id", id);

                    using (var reader = cmd.ExecuteReader())
                    {
                        if (reader.Read())
                        {
                            return new Utilisateur
                            {
                                Id = reader.GetInt32("id"),
                                Email = reader.GetString("email"),
                                Pseudo = reader.GetString("pseudo"),
                                Mdp = reader.GetString("mdp")
                            };
                        }
                    }
                }
            }

            return null;
        }

        public Utilisateur? GetByEmailAndPassword(string email, string mdp)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT id, email, pseudo, mdp FROM Utilisateur WHERE email = @email";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@email", email);

                    using (var reader = cmd.ExecuteReader())
                    {
                        if (reader.Read())
                        {
                            string storedHash = reader.GetString("mdp");
                            if (BCrypt.Net.BCrypt.Verify(mdp, storedHash))
                            {
                                return new Utilisateur
                                {
                                    Id = reader.GetInt32("id"),
                                    Email = reader.GetString("email"),
                                    Pseudo = reader.GetString("pseudo"),
                                    Mdp = storedHash
                                };
                            }
                        }
                    }
                }
            }
            return null;
        }

        public Utilisateur? GetByPseudoAndPassword(string pseudo, string mdp)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT id, email, pseudo, mdp FROM Utilisateur WHERE pseudo = @pseudo";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@pseudo", pseudo);

                    using (var reader = cmd.ExecuteReader())
                    {
                        if (reader.Read())
                        {
                            string storedHash = reader.GetString("mdp");
                            if (BCrypt.Net.BCrypt.Verify(mdp, storedHash))
                            {
                                return new Utilisateur
                                {
                                    Id = reader.GetInt32("id"),
                                    Email = reader.GetString("email"),
                                    Pseudo = reader.GetString("pseudo"),
                                    Mdp = storedHash
                                };
                            }
                        }
                    }
                }
            }
            return null;
        }

        public bool EmailExists(string email)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT COUNT(*) FROM Utilisateur WHERE email = @email";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@email", email);
                    return Convert.ToInt32(cmd.ExecuteScalar()) > 0;
                }
            }
        }

        public bool PseudoExists(string pseudo)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "SELECT COUNT(*) FROM Utilisateur WHERE pseudo = @pseudo";
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@pseudo", pseudo);
                    return Convert.ToInt32(cmd.ExecuteScalar()) > 0;
                }
            }
        }

        public bool Create(string pseudo, string email, string mdp)
        {
            using (var connection = DatabaseConnection.GetConnection())
            {
                connection.Open();
                string query = "INSERT INTO Utilisateur (pseudo, email, mdp) VALUES (@pseudo, @email, @mdp)";
                string hashedMdp = BCrypt.Net.BCrypt.HashPassword(mdp);
                
                using (var cmd = new MySqlCommand(query, connection))
                {
                    cmd.Parameters.AddWithValue("@pseudo", pseudo);
                    cmd.Parameters.AddWithValue("@email", email);
                    cmd.Parameters.AddWithValue("@mdp", hashedMdp);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }
    }
}
