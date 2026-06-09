using MySql.Data.MySqlClient;
using System;

namespace MVC_C_sharp.Data
{
    public class DatabaseConnection
    {
        private static readonly string[] connectionStrings =
        {
            "Server=104.40.137.99;Port=22260;Database=stadium_questionnaire;User Id=developer;Password=cerfal1313;",
            "Server=localhost;Port=3306;Database=Stadium_Questionnaire;User Id=root;Password=password;"
        };

        public static MySqlConnection GetConnection()
        {
            Exception? lastError = null;

            foreach (var connectionString in connectionStrings)
            {
                try
                {
                    var connection = new MySqlConnection(connectionString);
                    connection.Open();
                    return connection;
                }
                catch (Exception ex)
                {
                    lastError = ex;
                }
            }

            throw new Exception("Impossible de se connecter à la base locale ou distante.", lastError);
        }
    }
}