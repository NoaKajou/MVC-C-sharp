using MySql.Data.MySqlClient;

namespace MVC_C_sharp.Data
{
    public class DatabaseConnection
    {
        private static string connectionString = "Server=localhost;Database=Stadium_Questionnaire;User Id=root;Password=password;";

        public static MySqlConnection GetConnection()
        {
            return new MySqlConnection(connectionString);
        }
    }
}