<template>
    <div class="cooking-calendar">
        <div class="calendar-header">
            <h2 class="calendar-title">Календарь готовки</h2>
            <div class="calendar-controls">
                <button @click="previousMonth" class="btn-nav">&lt;</button>
                <span class="current-month">{{ currentMonthYear }}</span>
                <button @click="nextMonth" class="btn-nav">&gt;</button>
            </div>
        </div>

        <div class="calendar-grid">
            <div class="calendar-weekdays">
                <div v-for="day in weekdays" :key="day" class="weekday">{{ day }}</div>
            </div>
            <div class="calendar-days">
                <div
                    v-for="day in calendarDays"
                    :key="day.date"
                    :class="['calendar-day', {
                        'other-month': day.otherMonth,
                        'today': day.isToday,
                        'has-plans': day.hasPlans
                    }]"
                    @click="selectDate(day.date)"
                >
                    <div class="day-number">{{ day.day }}</div>
                    <div v-if="day.plans && day.plans.length > 0" class="day-plans">
                        <div
                            v-for="plan in day.plans.slice(0, 3)"
                            :key="plan.id"
                            class="plan-indicator"
                            :title="plan.recipe.title"
                        ></div>
                        <div v-if="day.plans.length > 3" class="plan-more">
                            +{{ day.plans.length - 3 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно для выбранной даты -->
        <div v-if="selectedDate" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Планы на {{ formatDate(selectedDate) }}</h3>
                    <button @click="closeModal" class="btn-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div v-if="selectedDatePlans.length === 0" class="no-plans">
                        <p>На эту дату нет запланированных блюд</p>
                        <button @click="showAddPlanForm = true" class="btn-add">Добавить блюдо</button>
                    </div>
                    <div v-else>
                        <div
                            v-for="plan in selectedDatePlans"
                            :key="plan.id"
                            class="plan-item"
                            :class="{ 'completed': plan.is_completed }"
                        >
                            <div class="plan-info">
                                <h4>{{ plan.recipe.title }}</h4>
                                <p v-if="plan.planned_time" class="plan-time">
                                    Время: {{ formatTime(plan.planned_time) }}
                                </p>
                                <p v-if="plan.servings" class="plan-servings">
                                    Порций: {{ plan.servings }}
                                </p>
                                <p v-if="plan.notes" class="plan-notes">{{ plan.notes }}</p>
                            </div>
                            <div class="plan-actions">
                                <button
                                    @click="toggleComplete(plan.id)"
                                    class="btn-toggle"
                                    :class="{ 'completed': plan.is_completed }"
                                >
                                    {{ plan.is_completed ? '✓' : '○' }}
                                </button>
                                <button @click="editPlan(plan)" class="btn-edit">✎</button>
                                <button @click="deletePlan(plan.id)" class="btn-delete">✕</button>
                            </div>
                        </div>
                        <button @click="showAddPlanForm = true" class="btn-add">Добавить блюдо</button>
                    </div>

                    <!-- Форма добавления/редактирования плана -->
                    <div v-if="showAddPlanForm" class="plan-form">
                        <h4>{{ editingPlan ? 'Редактировать план' : 'Добавить план' }}</h4>
                        <form @submit.prevent="savePlan">
                            <div class="form-group">
                                <label>Рецепт *</label>
                                <select v-model="formData.recipe_id" required>
                                    <option value="">Выберите рецепт</option>
                                    <option
                                        v-for="recipe in availableRecipes"
                                        :key="recipe.id"
                                        :value="recipe.id"
                                    >
                                        {{ recipe.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Дата *</label>
                                <input
                                    type="date"
                                    v-model="formData.planned_date"
                                    required
                                />
                            </div>
                            <div class="form-group">
                                <label>Время</label>
                                <input
                                    type="time"
                                    v-model="formData.planned_time"
                                />
                            </div>
                            <div class="form-group">
                                <label>Количество порций</label>
                                <input
                                    type="number"
                                    v-model.number="formData.servings"
                                    min="1"
                                />
                            </div>
                            <div class="form-group">
                                <label>Заметки</label>
                                <textarea
                                    v-model="formData.notes"
                                    rows="3"
                                ></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-save">Сохранить</button>
                                <button type="button" @click="cancelForm" class="btn-cancel">Отмена</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'CookingCalendar',
    data() {
        return {
            currentDate: new Date(),
            selectedDate: null,
            selectedDatePlans: [],
            showAddPlanForm: false,
            editingPlan: null,
            plans: [],
            availableRecipes: [],
            weekdays: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
            formData: {
                recipe_id: '',
                planned_date: '',
                planned_time: '',
                servings: 1,
                notes: ''
            }
        };
    },
    computed: {
        currentMonthYear() {
            return this.currentDate.toLocaleDateString('ru-RU', {
                month: 'long',
                year: 'numeric'
            });
        },
        calendarDays() {
            const year = this.currentDate.getFullYear();
            const month = this.currentDate.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = (firstDay.getDay() + 6) % 7; // Понедельник = 0

            const days = [];

            // Дни предыдущего месяца
            const prevMonthLastDay = new Date(year, month, 0).getDate();
            for (let i = startingDayOfWeek - 1; i >= 0; i--) {
                days.push({
                    day: prevMonthLastDay - i,
                    date: this.formatDateForApi(new Date(year, month - 1, prevMonthLastDay - i)),
                    otherMonth: true,
                    isToday: false,
                    hasPlans: false,
                    plans: []
                });
            }

            // Дни текущего месяца
            const today = new Date();
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dateStr = this.formatDateForApi(date);
                const dayPlans = this.plans.filter(p => p.planned_date === dateStr);
                
                days.push({
                    day: day,
                    date: dateStr,
                    otherMonth: false,
                    isToday: date.toDateString() === today.toDateString(),
                    hasPlans: dayPlans.length > 0,
                    plans: dayPlans
                });
            }

            // Дни следующего месяца
            const remainingDays = 42 - days.length; // 6 недель * 7 дней
            for (let day = 1; day <= remainingDays; day++) {
                days.push({
                    day: day,
                    date: this.formatDateForApi(new Date(year, month + 1, day)),
                    otherMonth: true,
                    isToday: false,
                    hasPlans: false,
                    plans: []
                });
            }

            return days;
        }
    },
    mounted() {
        this.loadPlans();
        this.loadRecipes();
    },
    methods: {
        async loadPlans() {
            try {
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth() + 1;
                const response = await axios.get('/api/cooking-calendar', {
                    params: { month, year }
                });
                this.plans = response.data.data || [];
            } catch (error) {
                console.error('Ошибка загрузки планов:', error);
            }
        },
        async loadRecipes() {
            try {
                // Предполагаем, что есть API endpoint для получения рецептов
                const response = await axios.get('/api/recipes');
                this.availableRecipes = response.data.data || response.data || [];
            } catch (error) {
                console.error('Ошибка загрузки рецептов:', error);
                // Если нет API, можно использовать другой endpoint
                try {
                    const response = await axios.get('/recipe-list');
                    // Парсим данные в зависимости от структуры ответа
                } catch (e) {
                    console.error('Не удалось загрузить рецепты');
                }
            }
        },
        previousMonth() {
            this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
            this.loadPlans();
        },
        nextMonth() {
            this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
            this.loadPlans();
        },
        async selectDate(date) {
            this.selectedDate = date;
            this.showAddPlanForm = false;
            this.editingPlan = null;
            try {
                const response = await axios.get(`/api/cooking-calendar/date/${date}`);
                this.selectedDatePlans = response.data.data || [];
            } catch (error) {
                console.error('Ошибка загрузки планов на дату:', error);
                this.selectedDatePlans = [];
            }
        },
        closeModal() {
            this.selectedDate = null;
            this.showAddPlanForm = false;
            this.editingPlan = null;
            this.resetForm();
        },
        editPlan(plan) {
            this.editingPlan = plan;
            this.formData = {
                recipe_id: plan.recipe_id,
                planned_date: plan.planned_date,
                planned_time: plan.planned_time ? plan.planned_time.substring(0, 5) : '',
                servings: plan.servings || 1,
                notes: plan.notes || ''
            };
            this.showAddPlanForm = true;
        },
        async savePlan() {
            try {
                const url = this.editingPlan
                    ? `/api/cooking-calendar/${this.editingPlan.id}`
                    : '/api/cooking-calendar';
                const method = this.editingPlan ? 'put' : 'post';

                const response = await axios[method](url, this.formData);
                
                if (response.data.success) {
                    await this.loadPlans();
                    if (this.selectedDate) {
                        await this.selectDate(this.selectedDate);
                    }
                    this.closeModal();
                }
            } catch (error) {
                console.error('Ошибка сохранения плана:', error);
                alert('Ошибка сохранения плана');
            }
        },
        async deletePlan(planId) {
            if (!confirm('Вы уверены, что хотите удалить этот план?')) {
                return;
            }
            try {
                await axios.delete(`/api/cooking-calendar/${planId}`);
                await this.loadPlans();
                if (this.selectedDate) {
                    await this.selectDate(this.selectedDate);
                }
            } catch (error) {
                console.error('Ошибка удаления плана:', error);
                alert('Ошибка удаления плана');
            }
        },
        async toggleComplete(planId) {
            try {
                const response = await axios.patch(`/api/cooking-calendar/${planId}/toggle-complete`);
                if (response.data.success && this.selectedDate) {
                    await this.selectDate(this.selectedDate);
                }
            } catch (error) {
                console.error('Ошибка изменения статуса:', error);
            }
        },
        cancelForm() {
            this.showAddPlanForm = false;
            this.editingPlan = null;
            this.resetForm();
        },
        resetForm() {
            this.formData = {
                recipe_id: '',
                planned_date: this.selectedDate || '',
                planned_time: '',
                servings: 1,
                notes: ''
            };
        },
        formatDate(date) {
            return new Date(date + 'T00:00:00').toLocaleDateString('ru-RU', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        formatTime(time) {
            if (!time) return '';
            return time.substring(0, 5);
        },
        formatDateForApi(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    }
};
</script>

<style scoped>
.cooking-calendar {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.calendar-title {
    margin: 0;
    font-size: 24px;
    color: #333;
}

.calendar-controls {
    display: flex;
    align-items: center;
    gap: 15px;
}

.btn-nav {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}

.btn-nav:hover {
    background: #0056b3;
}

.current-month {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    min-width: 200px;
    text-align: center;
}

.calendar-grid {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.weekday {
    padding: 10px;
    text-align: center;
    font-weight: 600;
    color: #495057;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.calendar-day {
    min-height: 100px;
    padding: 8px;
    border: 1px solid #dee2e6;
    cursor: pointer;
    transition: background 0.2s;
    position: relative;
}

.calendar-day:hover {
    background: #f8f9fa;
}

.calendar-day.other-month {
    background: #f8f9fa;
    color: #adb5bd;
}

.calendar-day.today {
    background: #e7f3ff;
    font-weight: 600;
}

.calendar-day.has-plans {
    background: #fff3cd;
}

.day-number {
    font-size: 14px;
    margin-bottom: 5px;
}

.day-plans {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
    margin-top: 5px;
}

.plan-indicator {
    width: 8px;
    height: 8px;
    background: #007bff;
    border-radius: 50%;
}

.plan-more {
    font-size: 10px;
    color: #6c757d;
    margin-left: 5px;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
}

.modal-header h3 {
    margin: 0;
}

.btn-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6c757d;
}

.modal-body {
    padding: 20px;
}

.no-plans {
    text-align: center;
    padding: 40px;
}

.plan-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 15px;
    margin-bottom: 10px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    background: #f8f9fa;
}

.plan-item.completed {
    opacity: 0.6;
    text-decoration: line-through;
}

.plan-info h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.plan-time, .plan-servings, .plan-notes {
    margin: 5px 0;
    font-size: 14px;
    color: #6c757d;
}

.plan-actions {
    display: flex;
    gap: 5px;
}

.btn-toggle, .btn-edit, .btn-delete {
    background: none;
    border: 1px solid #dee2e6;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-toggle.completed {
    background: #28a745;
    color: white;
}

.btn-edit:hover {
    background: #ffc107;
    color: white;
}

.btn-delete:hover {
    background: #dc3545;
    color: white;
}

.btn-add {
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 15px;
    transition: background 0.3s;
}

.btn-add:hover {
    background: #218838;
}

.plan-form {
    margin-top: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 5px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 14px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn-save {
    background: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-save:hover {
    background: #0056b3;
}

.btn-cancel {
    background: #6c757d;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-cancel:hover {
    background: #5a6268;
}
</style>
