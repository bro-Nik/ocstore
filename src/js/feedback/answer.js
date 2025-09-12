/**
 * Модуль работы с вопросами-ответами
 * @module Answer
 */
import { FeedbackBase } from './base';

const CONFIG = {
  moduleName: 'answer',
  endpoints: {
    load: 'index.php?route=product/product/getAnswers&revproduct_id=',
    write: 'index.php?route=product/product/writeAnswer&revproduct_id=',
  },
  selectors: {
    container: '.answers_container',
    content: '#answers',
    pagination: '.pagination a',
    loadTrigger: "#load_answers"
  },
};

class Answer extends FeedbackBase {
  constructor() {
    super(CONFIG);
  }
}

export const answer = new Answer();
