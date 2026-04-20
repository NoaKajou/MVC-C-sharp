using MVC_C_sharp.Models;
using PdfSharpCore.Drawing;
using PdfSharpCore.Pdf;

namespace MVC_C_sharp.Services
{
    public class PdfExportService
    {
        public void ExportQuestionnaire(Stream outputStream, Questionnaire questionnaire, List<Question> questions, Dictionary<int, List<Reponse>> reponsesByQuestion)
        {
            var document = new PdfDocument();
            document.Info.Title = questionnaire.Nom;

            var titleFont = new XFont("Helvetica", 16, XFontStyle.Bold);
            var normalFont = new XFont("Helvetica", 11, XFontStyle.Regular);
            var italicFont = new XFont("Helvetica", 10, XFontStyle.Italic);

            var page = document.AddPage();
            var gfx = XGraphics.FromPdfPage(page);
            double y = 40;
            double left = 40;
            double right = page.Width - 40;
            double width = right - left;

            DrawWrappedLine(gfx, questionnaire.Nom, titleFont, left, ref y, width, 22, page, document, ref gfx);
            DrawWrappedLine(gfx, $"Theme: {questionnaire.Theme} | Questions: {questions.Count} | Statut: {questionnaire.StatutPublication}", normalFont, left, ref y, width, 16, page, document, ref gfx);
            y += 8;

            foreach (var question in questions.OrderBy(q => q.Numero))
            {
                DrawWrappedLine(gfx, $"Question {question.Numero}: {question.Libelle}", normalFont, left, ref y, width, 16, page, document, ref gfx);

                if (question.TypeReponse == "VraiFaux")
                {
                    string expected = question.ReponseVraiFaux == true ? "Vrai" : "Faux";
                    DrawWrappedLine(gfx, $"   Reponse attendue: {expected}", italicFont, left, ref y, width, 14, page, document, ref gfx);
                }
                else
                {
                    if (reponsesByQuestion.TryGetValue(question.Id, out var reponses) && reponses.Count > 0)
                    {
                        foreach (var reponse in reponses)
                        {
                            var mark = reponse.EstCorrecte ? "(Correcte)" : "";
                            DrawWrappedLine(gfx, $"   - {reponse.Valeur} {mark}".TrimEnd(), normalFont, left, ref y, width, 14, page, document, ref gfx);
                        }
                    }
                    else
                    {
                        DrawWrappedLine(gfx, "   - Aucune reponse configuree", italicFont, left, ref y, width, 14, page, document, ref gfx);
                    }
                }

                y += 6;
            }

            document.Save(outputStream, false);
        }

        private void DrawWrappedLine(XGraphics gfx, string text, XFont font, double x, ref double y, double maxWidth, double lineHeight, PdfPage page, PdfDocument document, ref XGraphics currentGfx)
        {
            foreach (var line in WrapText(gfx, text, font, maxWidth))
            {
                if (y > page.Height - 40)
                {
                    page = document.AddPage();
                    currentGfx = XGraphics.FromPdfPage(page);
                    gfx = currentGfx;
                    y = 40;
                }

                gfx.DrawString(line, font, XBrushes.Black, new XPoint(x, y));
                y += lineHeight;
            }
        }

        private List<string> WrapText(XGraphics gfx, string text, XFont font, double maxWidth)
        {
            var lines = new List<string>();
            var words = (text ?? string.Empty).Split(' ');
            var current = string.Empty;

            foreach (var word in words)
            {
                var candidate = string.IsNullOrEmpty(current) ? word : $"{current} {word}";
                var size = gfx.MeasureString(candidate, font);
                if (size.Width <= maxWidth)
                {
                    current = candidate;
                }
                else
                {
                    if (!string.IsNullOrEmpty(current))
                    {
                        lines.Add(current);
                    }
                    current = word;
                }
            }

            if (!string.IsNullOrEmpty(current))
            {
                lines.Add(current);
            }

            if (lines.Count == 0)
            {
                lines.Add(string.Empty);
            }

            return lines;
        }
    }
}
