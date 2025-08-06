<?php
class ControllerRevolutionCarouselReview extends Controller
{
    public function index($settings) {
		
		if (!$settings['status']) {
			return false;
		}
		
		$data['title'] = $settings['title'];
		$data['button_all'] = (int)$settings['button_all'];
        $data['button_all_text'] = html_entity_decode($settings['button_all_text'], ENT_QUOTES, 'UTF-8');
        $data['keyword'] = $this->url->link('revolution/revstorereview');
        $this->load->model('revolution/revolution');

        $results = $this->model_revolution_revolution->getModuleReviews(0, $settings['limit'], $settings['order']);

        if ($results) {
            foreach ($results as $result) {
                $data['reviews'][] = array(
                    'review_id'  => $result['review_id'],
                    'text' 		 => utf8_substr(strip_tags(html_entity_decode($result['text'], ENT_QUOTES, 'UTF-8')), 0, (int)$settings['limit_text']) . '..',
                    'rating' 	 => (int)$result['rating'],
                    'author' 	 => $result['author'],
                    'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
                );
            }

	    return $this->load->view('revolution/carousel_review', $data);
                
            }
        }
}
