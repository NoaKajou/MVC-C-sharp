using Avalonia;
using System;
using MVC_C_sharp.Data;

namespace MVC_C_sharp
{
    class Program
    {
        [STAThread]
        public static void Main(string[] args)
        {
            DatabaseInitializer.EnsureAdminAndLogTables();

            BuildAvaloniaApp()
                .StartWithClassicDesktopLifetime(args);
        }

        public static AppBuilder BuildAvaloniaApp()
            => AppBuilder.Configure<App>()
                .UsePlatformDetect()
                .WithInterFont()
                .LogToTrace();
    }
}
