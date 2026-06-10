// Utility functions
function showMessage(message, type = 'error') {
    const existingMessages = document.querySelectorAll('.error-message, .success-message');
    existingMessages.forEach(msg => msg.remove());
    
    const div = document.createElement('div');
    div.className = type === 'error' ? 'error-message' : 'success-message';
    div.textContent = message;
    
    const container = document.querySelector('.auth-container, .edit-container, .page-container');
    if (container) {
        container.insertBefore(div, container.firstChild);
    }
    
    setTimeout(() => div.remove(), 5000);
}

function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#E74C3C';
                    valid = false;
                } else {
                    field.style.borderColor = '#444';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                showMessage('Veuillez remplir tous les champs obligatoires');
            }
        });
    });
    
    // Password confirmation
    const confirmPassword = document.querySelector('input[name="confirm_mdp"]');
    if (confirmPassword) {
        confirmPassword.addEventListener('input', () => {
            const password = document.querySelector('input[name="mdp"]');
            if (password.value !== confirmPassword.value) {
                confirmPassword.style.borderColor = '#E74C3C';
            } else {
                confirmPassword.style.borderColor = '#27AE60';
            }
        });
    }
}

async function initDbStatusBadge() {
    const badge = document.getElementById('dbStatusBadge');
    if (!badge) {
        return;
    }

    try {
        const response = await fetch('api/db_status.php', { cache: 'no-store' });
        const data = await response.json();

        badge.classList.remove('db-status-loading', 'db-status-online', 'db-status-offline');
        if (data.connected) {
            badge.classList.add('db-status-online');
            const source = data.source === 'locale' ? 'locale' : 'distante';
            badge.textContent = `En ligne (${source})`;
        } else {
            badge.classList.add('db-status-offline');
            badge.textContent = 'Hors ligne';
        }
    } catch {
        badge.classList.remove('db-status-loading', 'db-status-online');
        badge.classList.add('db-status-offline');
        badge.textContent = 'Hors ligne';
    }
}

function initQuestionnairesVue() {
    const mountPoint = document.getElementById('questionnairesApp');
    if (!mountPoint || !window.Vue || !window.__QUESTIONNAIRES_PAGE__) {
        return;
    }

    const { createApp } = window.Vue;
    const pageData = window.__QUESTIONNAIRES_PAGE__;

    createApp({
        data() {
            return {
                searchAll: '',
                searchMine: '',
                selectedAllId: null,
                selectedMyId: null,
                allQuestionnaires: pageData.allQuestionnaires || [],
                myQuestionnaires: pageData.myQuestionnaires || []
            };
        },
        computed: {
            filteredAllQuestionnaires() {
                const query = this.searchAll.trim().toLowerCase();
                if (!query) {
                    return this.allQuestionnaires;
                }

                return this.allQuestionnaires.filter((questionnaire) => {
                    return [questionnaire.nom, questionnaire.theme]
                        .some((value) => String(value).toLowerCase().includes(query));
                });
            },
            filteredMyQuestionnaires() {
                const query = this.searchMine.trim().toLowerCase();
                if (!query) {
                    return this.myQuestionnaires;
                }

                return this.myQuestionnaires.filter((questionnaire) => {
                    return [questionnaire.nom, questionnaire.theme]
                        .some((value) => String(value).toLowerCase().includes(query));
                });
            }
        },
        methods: {
            selectAllQuestionnaire(id) {
                this.selectedAllId = id;
            },
            selectMyQuestionnaire(id) {
                this.selectedMyId = id;
            },
            playSelectedQuestionnaire() {
                if (this.selectedAllId) {
                    window.location.href = `play.php?id=${this.selectedAllId}`;
                }
            },
            editSelectedQuestionnaire() {
                if (this.selectedMyId) {
                    window.location.href = `questionnaire_edit.php?id=${this.selectedMyId}`;
                }
            },
            publishSelectedQuestionnaire() {
                if (this.selectedMyId) {
                    window.location.href = `questionnaire_publish.php?id=${this.selectedMyId}`;
                }
            },
            exportSelectedQuestionnaire() {
                if (this.selectedMyId) {
                    window.location.href = `questionnaire_export.php?id=${this.selectedMyId}`;
                }
            },
            deleteSelectedQuestionnaire() {
                if (this.selectedMyId && confirm('Êtes-vous sûr de vouloir supprimer ce questionnaire ?')) {
                    window.location.href = `questionnaire_delete.php?id=${this.selectedMyId}`;
                }
            }
        }
    }).mount('#questionnairesApp');
}

function initLoginVue() {
    const mountPoint = document.getElementById('loginApp');
    if (!mountPoint || !window.Vue) {
        return;
    }

    const pageData = window.__LOGIN_PAGE__ || {};

    window.Vue.createApp({
        data() {
            return {
                identifiant: '',
                mdp: '',
                error: pageData.error || '',
                success: pageData.success || ''
            };
        },
        computed: {
            canSubmit() {
                return this.identifiant.trim() && this.mdp.trim();
            }
        }
    }).mount('#loginApp');
}

function initRegisterVue() {
    const mountPoint = document.getElementById('registerApp');
    if (!mountPoint || !window.Vue) {
        return;
    }

    const pageData = window.__REGISTER_PAGE__ || {};

    window.Vue.createApp({
        data() {
            return {
                pseudo: '',
                email: '',
                roleId: '',
                mdp: '',
                confirmMdp: '',
                error: pageData.error || '',
                success: pageData.success || ''
            };
        },
        computed: {
            passwordsMatch() {
                return !this.mdp || !this.confirmMdp || this.mdp === this.confirmMdp;
            },
            canSubmit() {
                return this.pseudo.trim() && this.email.trim() && this.roleId && this.mdp.trim() && this.confirmMdp.trim() && this.passwordsMatch;
            }
        }
    }).mount('#registerApp');
}

function initAccueilVue() {
    const mountPoint = document.getElementById('accueilApp');
    if (!mountPoint || !window.Vue) {
        return;
    }

    const pageData = window.__ACCUEIL_PAGE__ || {};

    window.Vue.createApp({
        data() {
            return {
                userPseudo: pageData.userPseudo || ''
            };
        }
    }).mount('#accueilApp');
}

function initProfilVue() {
    const mountPoint = document.getElementById('profilApp');
    if (!mountPoint || !window.Vue) {
        return;
    }

    const pageData = window.__PROFIL_PAGE__ || {};

    window.Vue.createApp({
        data() {
            return {
                user: pageData.user || {},
                questionnaires: pageData.questionnaires || [],
                history: pageData.history || [],
                searchQuestionnaires: '',
                searchHistory: ''
            };
        },
        computed: {
            filteredQuestionnaires() {
                const query = this.searchQuestionnaires.trim().toLowerCase();
                if (!query) {
                    return this.questionnaires;
                }

                return this.questionnaires.filter((questionnaire) => {
                    return [questionnaire.nom, questionnaire.theme]
                        .some((value) => String(value).toLowerCase().includes(query));
                });
            },
            filteredHistory() {
                const query = this.searchHistory.trim().toLowerCase();
                if (!query) {
                    return this.history;
                }

                return this.history.filter((entry) => {
                    return [entry.questionnaire_nom, entry.questionnaire_theme, entry.auteur_pseudo]
                        .some((value) => String(value).toLowerCase().includes(query));
                });
            }
        }
    }).mount('#profilApp');
}

function initQuestionnaireEditVue() {
    const mountPoint = document.getElementById('questionnaireEditApp');
    if (!mountPoint || !window.Vue) {
        return;
    }

    const pageData = window.__QUESTIONNAIRE_EDIT_PAGE__ || {};

    window.Vue.createApp({
        data() {
            return {
                questionnaire: pageData.questionnaire || { id: null, nom: '', theme: '', niveau: 1, estPublie: false },
                questions: pageData.questions || [],
                error: pageData.error || ''
            };
        },
        computed: {
            isEditing() {
                return Boolean(this.questionnaire && this.questionnaire.id);
            }
        }
    }).mount('#questionnaireEditApp');
}

function initQuestionEditVue() {
    const mountPoint = document.getElementById('questionEditApp');
    if (!mountPoint || !window.Vue) {
        return;
    }

    const pageData = window.__QUESTION_EDIT_PAGE__ || {};

    window.Vue.createApp({
        data() {
            return {
                questionnaireId: pageData.questionnaireId || null,
                question: pageData.question || null,
                error: pageData.error || '',
                libelle: pageData.question?.libelle || '',
                typeReponse: pageData.question?.typeReponse || 'VraiFaux',
                reponseVraiFaux: pageData.question?.reponseVraiFaux ? '1' : '0',
                responses: (pageData.reponses || []).map((response) => ({
                    valeur: response.valeur,
                    estCorrecte: Number(response.estCorrecte) === 1 ? '1' : '0'
                })),
                nouvelleValeur: '',
                estCorrecte: false
            };
        },
        computed: {
            showVraiFauxPanel() {
                return this.typeReponse === 'VraiFaux';
            },
            showListePanel() {
                return this.typeReponse === 'ListeValeurs';
            }
        },
        methods: {
            addResponse() {
                const value = this.nouvelleValeur.trim();
                if (!value) {
                    return;
                }

                this.responses.push({
                    valeur: value,
                    estCorrecte: this.estCorrecte ? '1' : '0'
                });

                this.nouvelleValeur = '';
                this.estCorrecte = false;
            },
            removeResponse(index) {
                this.responses.splice(index, 1);
            }
        }
    }).mount('#questionEditApp');
}

function initPlayVue() {
    const mountPoint = document.getElementById('playApp');
    if (!mountPoint || !window.Vue || !window.Chart) {
        return;
    }

    const pageData = window.__PLAY_PAGE__ || {};

    window.Vue.createApp({
        data() {
            return {
                questionnaire: pageData.questionnaire || {},
                questions: pageData.questions || [],
                reponses: pageData.reponses || {},
                questionnaireId: pageData.questionnaireId || null,
                currentIndex: 0,
                answers: {},
                score: 0,
                resultVisible: false,
                modalVisible: false,
                signalDescription: '',
                signalFeedback: '',
                signalFeedbackSuccess: false,
                signalSending: false,
                resultChart: null
            };
        },
        computed: {
            currentQuestion() {
                return this.questions[this.currentIndex] || null;
            },
            totalQuestions() {
                return this.questions.length;
            },
            currentResponses() {
                if (!this.currentQuestion) {
                    return [];
                }

                return this.reponses[this.currentQuestion.id] || [];
            },
            isLastQuestion() {
                return this.currentIndex === this.questions.length - 1;
            }
        },
        methods: {
            isAnswerCorrect(question, answer) {
                if (!question || answer === undefined || answer === null) {
                    return false;
                }

                if (question.typeReponse === 'VraiFaux') {
                    const expected = question.reponseVraiFaux ? 'true' : 'false';
                    return answer === expected;
                }

                const qResponses = this.reponses[question.id] || [];
                const matchedResponse = qResponses.find((response) => String(response.id) === String(answer));
                return Boolean(matchedResponse && Number(matchedResponse.estCorrecte) === 1);
            },
            async persistAnswer(question) {
                const answer = this.answers[question.id];
                if (!answer) {
                    return;
                }

                await fetch('api/check_answer.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ questionId: question.id, questionnaireId: this.questionnaireId, answer })
                }).catch(() => null);
            },
            async calculateScore() {
                this.score = 0;

                const tasks = [];
                for (const question of this.questions) {
                    const answer = this.answers[question.id];
                    if (answer !== undefined && answer !== null && answer !== '') {
                        if (this.isAnswerCorrect(question, answer)) {
                            this.score++;
                        }
                        tasks.push(this.persistAnswer(question));
                    }
                }

                await Promise.allSettled(tasks);
                this.showResult();
            },
            showResult() {
                this.resultVisible = true;
                this.$nextTick(() => {
                    this.renderResultChart();
                });
            },
            renderResultChart() {
                if (!this.$refs.resultChart) {
                    return;
                }

                const wrongAnswers = this.questions.length - this.score;

                if (this.resultChart) {
                    this.resultChart.destroy();
                }

                this.resultChart = new window.Chart(this.$refs.resultChart, {
                    type: 'pie',
                    data: {
                        labels: ['Bonnes réponses', 'Mauvaises réponses'],
                        datasets: [{
                            data: [this.score, wrongAnswers],
                            backgroundColor: ['#1f9d55', '#d64545'],
                            borderColor: ['#ffffff', '#ffffff'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            },
            nextQuestion() {
                if (this.currentIndex < this.questions.length - 1) {
                    this.currentIndex++;
                    return;
                }

                this.calculateScore();
            },
            previousQuestion() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                }
            },
            quitQuestionnaire() {
                if (confirm('Êtes-vous sûr de vouloir quitter ?')) {
                    window.location.href = 'questionnaires.php';
                }
            },
            openSignalModal() {
                if (!this.currentQuestion) {
                    return;
                }

                this.signalDescription = '';
                this.signalFeedback = '';
                this.signalFeedbackSuccess = false;
                this.modalVisible = true;
            },
            closeSignalModal() {
                this.modalVisible = false;
            },
            async sendSignal() {
                const description = this.signalDescription.trim();
                if (!description || !this.currentQuestion) {
                    this.signalFeedback = 'Veuillez décrire le problème.';
                    this.signalFeedbackSuccess = false;
                    return;
                }

                this.signalSending = true;

                try {
                    const response = await fetch('api/report_question.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ questionId: this.currentQuestion.id, description })
                    });
                    const data = await response.json();

                    this.signalFeedback = data.message;
                    this.signalFeedbackSuccess = data.success === true;

                    if (data.success) {
                        setTimeout(() => {
                            this.modalVisible = false;
                        }, 1800);
                    }
                } catch {
                    this.signalFeedback = 'Erreur réseau, veuillez réessayer.';
                    this.signalFeedbackSuccess = false;
                } finally {
                    this.signalSending = false;
                }
            }
        }
    }).mount('#playApp');
}

function initStatsVue() {
    const mountPoint = document.getElementById('statsApp');
    if (!mountPoint || !window.Vue || !window.Chart) {
        return;
    }

    const pageData = window.__STATS_PAGE__ || {};

    window.Vue.createApp({
        data() {
            return {
                questionnairesByTheme: pageData.questionnairesByTheme || [],
                weeklyConnections: pageData.weeklyConnections || [],
                successByTheme: pageData.successByTheme || [],
                successByThemeFallback: (pageData.successByTheme && pageData.successByTheme.length > 0)
                    ? pageData.successByTheme
                    : (pageData.questionnairesByTheme || []).map((item) => ({ theme: item.theme, taux: 0 })),
                charts: []
            };
        },
        mounted() {
            this.renderCharts();
        },
        methods: {
            getPalette(size) {
                const palette = [
                    '#0e8b7d', '#1f9d55', '#2f80ed', '#f59e0b', '#ef4444',
                    '#14b8a6', '#f97316', '#22c55e', '#8b5cf6', '#0ea5e9'
                ];
                return Array.from({ length: size }, (_, index) => palette[index % palette.length]);
            },
            renderCharts() {
                const chartQuestionnaires = document.getElementById('chartQuestionnairesByTheme');
                const chartWeekly = document.getElementById('chartWeeklyConnections');
                const chartSuccess = document.getElementById('chartSuccessByTheme');

                if (chartQuestionnaires && this.questionnairesByTheme.length > 0) {
                    const labels = this.questionnairesByTheme.map((item) => item.theme);
                    const values = this.questionnairesByTheme.map((item) => item.total);
                    this.charts.push(new window.Chart(chartQuestionnaires, {
                        type: 'doughnut',
                        data: {
                            labels,
                            datasets: [{
                                data: values,
                                backgroundColor: this.getPalette(values.length),
                                borderColor: '#ffffff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    }));
                }

                if (chartWeekly && this.weeklyConnections.length > 0) {
                    const labels = this.weeklyConnections.map((item) => item.jour);
                    const values = this.weeklyConnections.map((item) => item.total);
                    this.charts.push(new window.Chart(chartWeekly, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Utilisateurs actifs',
                                data: values,
                                backgroundColor: '#2f80ed'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            },
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    }));
                }

                if (chartSuccess && this.successByThemeFallback.length > 0) {
                    const labels = this.successByThemeFallback.map((item) => item.theme);
                    const values = this.successByThemeFallback.map((item) => item.taux);
                    this.charts.push(new window.Chart(chartSuccess, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Taux de succès (%)',
                                data: values,
                                backgroundColor: '#1f9d55'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    min: 0,
                                    max: 100,
                                    ticks: {
                                        callback: (value) => value + '%'
                                    }
                                }
                            }
                        }
                    }));
                }
            }
        }
    }).mount('#statsApp');
}

function bootstrapApp() {
    setupFormValidation();
    initDbStatusBadge();
    initQuestionnairesVue();
    initLoginVue();
    initRegisterVue();
    initAccueilVue();
    initProfilVue();
    initQuestionnaireEditVue();
    initQuestionEditVue();
    initPlayVue();
    initStatsVue();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrapApp);
} else {
    bootstrapApp();
}

// API helper
async function apiCall(url, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: 'Erreur de connexion' };
    }
}
