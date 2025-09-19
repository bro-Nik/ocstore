<?php
class ModelToolImage extends Model {
	public function resize($filename, $width, $height) {
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.' . $extension;
		$image_new_webp = 'cache/webp/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.webp';
		$use_webp = false;

		// Проверяем поддержку WebP браузером
		if (stripos($this->request->server['REQUEST_URI'], 'admin') === false && 
			isset($this->request->server['HTTP_ACCEPT']) && 
			strpos($this->request->server['HTTP_ACCEPT'], 'image/webp') !== false) {
			$use_webp = true;
		}

		if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
				 
			if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_WEBP))) { 
				return $this->getImageUrl($image_old);
			}
						
			// Создаем директории для кэша
			$path = '';
			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			// Обрабатываем изображение
			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image(DIR_IMAGE . $image_old);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $image_new);
			} else {
				copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
			}

			// Вотермарк
			// if ($this->config->get('revtheme_all_settings')['watermark_status']) {
			// 	$this->load->model('revolution/revolution');
			// 	$this->model_revolution_revolution->revwatermark($image_old, $image_new);
			// }

			// Создаем WebP
			$gd_info = gd_info();
			if($gd_info['WebP Support']) {
				if (is_file(DIR_IMAGE . $image_new) && (!is_file(DIR_IMAGE . $image_new_webp) || (filemtime(DIR_IMAGE . $image_new) > filemtime(DIR_IMAGE . $image_new_webp)))) {

					// Создаем директории для WebP
					$path = '';
					$directories = explode('/', dirname($image_new_webp));
					foreach ($directories as $directory) {
						$path = $path . '/' . $directory;

						if (!is_dir(DIR_IMAGE . $path)) {
							@mkdir(DIR_IMAGE . $path, 0755);
						}
					}

					// Создаем WebP
					$extension = strtolower($extension);
					if ($extension == 'gif') {
						$img = imagecreatefromgif(DIR_IMAGE.$image_new);
					} elseif ($extension == 'png') {
						$img = imagecreatefrompng(DIR_IMAGE.$image_new);
					} elseif ($extension == 'jpeg' || $extension == 'jpg') {
						$img = imagecreatefromjpeg(DIR_IMAGE.$image_new);
					} else {
						$img = '';
					}

					if($img) {
						imagepalettetotruecolor($img);
						imagewebp($img, DIR_IMAGE.$image_new_webp);
						imagedestroy($img);
					}
				}
			}
		}

		// Определяем какой файл возвращать
		$return_image = $image_new;
		if ($use_webp && is_file(DIR_IMAGE . $image_new_webp)) {
			$return_image = $image_new_webp;
		}
		$return_image = str_replace(' ', '%20', $return_image);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
		
		return $this->getImageUrl($return_image);
	}

	protected function getImageUrl($image_path) {
		if ($this->request->server['HTTPS']) {
			return $this->config->get('config_ssl') . 'image/' . $image_path;
		} else {
			return $this->config->get('config_url') . 'image/' . $image_path;
		}
	}
}
