using MVC_C_sharp.Repositories;

namespace MVC_C_sharp.Data
{
    public static class DatabaseInitializer
    {
        private static bool _initialized;

        public static void EnsureAdminAndLogTables()
        {
            if (_initialized)
            {
                return;
            }

            var questionnaireRepository = new QuestionnaireRepository();
            questionnaireRepository.EnsureFeatureSchema();

            var adminRepository = new AdminRepository();
            adminRepository.EnsureAdminTable();

            var adminLogRepository = new AdminLogRepository();
            adminLogRepository.EnsureLogTable();

            _initialized = true;
        }
    }
}
