<?php
class ModelToolImage extends Model {
	public function resize($filename, $width, $height) {
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.' . $extension;

		if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
				 
			if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_WEBP))) { 
				if ($this->request->server['HTTPS']) {
					return $this->config->get('config_ssl') . 'image/' . $image_old;
 				} else {
					return $this->config->get('config_url') . 'image/' . $image_old;
				}
			}
						
			$path = '';

			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image(DIR_IMAGE . $image_old);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $image_new);
			} else {
				copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);

				if ($this->config->get('revtheme_all_settings')['watermark_status']) {
					$this->load->model('revolution/revolution');
					$this->model_revolution_revolution->revwatermark($image_old, $image_new);
				}
				if (!empty($this->config->get('revtheme_all_settings')['webp_on'])) {
					$gd_info = gd_info();
					if($gd_info['WebP Support']) {
						$image_new_webp = 'cache/webp/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.webp';
						if (is_file(DIR_IMAGE . $image_new) && (!is_file(DIR_IMAGE . $image_new_webp) || (filemtime(DIR_IMAGE . $image_new) > filemtime(DIR_IMAGE . $image_new_webp)))) {
							$path = '';
							$directories = explode('/', dirname($image_new_webp));
							foreach ($directories as $directory) {
								$path = $path . '/' . $directory;

								if (!is_dir(DIR_IMAGE . $path)) {
									@mkdir(DIR_IMAGE . $path, 0755);
								}
							}
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
						if(is_file(DIR_IMAGE . $image_new_webp)) {
							if(stripos($this->request->server['REQUEST_URI'], 'admin') === false && isset($this->request->server['HTTP_ACCEPT']) && strpos($this->request->server['HTTP_ACCEPT'], 'image/webp') !== false) {
								$image_new = $image_new_webp;
							}
						}
					}
				}
			}
		}
		
		$image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
		
		if ($this->request->server['HTTPS']) {
			return $this->config->get('config_ssl') . 'image/' . $image_new;
		} else {
			return $this->config->get('config_url') . 'image/' . $image_new;
		}
	}

	public function resizeToHeight($filename, $target_height) {
    if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != DIR_IMAGE) {
        return;
    }

    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $image_old = $filename;
    $image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$target_height . '.' . $extension;

    if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
        list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
        
        // Рассчитываем пропорциональную ширину
        $ratio = $width_orig / $height_orig;
        $target_width = round($target_height * $ratio);

        $iimmgg = getimagesize(DIR_IMAGE . $image_old);
        if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF)) && (isset($iimmgg['mime']) && ($iimmgg['mime'] != 'image/svg+xml'))) { 
            return DIR_IMAGE . $image_old;
        }
        
        $path = '';
        $directories = explode('/', dirname($image_new));

        foreach ($directories as $directory) {
            $path = $path . '/' . $directory;

            if (!is_dir(DIR_IMAGE . $path)) {
                @mkdir(DIR_IMAGE . $path, 0777);
            }
        }

        if ($width_orig != $target_width || $height_orig != $target_height) {
            $image = new Image(DIR_IMAGE . $image_old);
            $image->resize($target_width, $target_height);
            $image->save(DIR_IMAGE . $image_new);
        } else {
          copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);

					if ($this->config->get('revtheme_all_settings')['watermark_status']) {
						$this->load->model('revolution/revolution');
						$this->model_revolution_revolution->revwatermark($image_old, $image_new);
					}
					if (!empty($this->config->get('revtheme_all_settings')['webp_on'])) {
						$gd_info = gd_info();
						if($gd_info['WebP Support']) {
							$image_new_webp = 'cache/webp/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$target_width . 'x' . (int)$target_height . '.webp';
							if (is_file(DIR_IMAGE . $image_new) && (!is_file(DIR_IMAGE . $image_new_webp) || (filemtime(DIR_IMAGE . $image_new) > filemtime(DIR_IMAGE . $image_new_webp)))) {
								$path = '';
								$directories = explode('/', dirname($image_new_webp));
								foreach ($directories as $directory) {
									$path = $path . '/' . $directory;

									if (!is_dir(DIR_IMAGE . $path)) {
										@mkdir(DIR_IMAGE . $path, 0755);
									}
								}
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
							if(is_file(DIR_IMAGE . $image_new_webp)) {
								if(stripos($this->request->server['REQUEST_URI'], 'admin') === false && isset($this->request->server['HTTP_ACCEPT']) && strpos($this->request->server['HTTP_ACCEPT'], 'image/webp') !== false) {
									$image_new = $image_new_webp;
								}
							}
						}
					}
        }

        if ($this->config->get('revtheme_all_settings')['watermark_status']) {
            $this->load->model('revolution/revolution');
            $this->model_revolution_revolution->revwatermark($image_old, $image_new);
        }
        
        if (!empty($this->config->get('revtheme_all_settings')['webp_on'])) {
            $gd_info = gd_info();
            if($gd_info['WebP Support']) {
                // Используем target_width и target_height вместо неопределенных переменных
                $image_new_webp = 'cache/webp/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$target_width . 'x' . (int)$target_height . '.webp';
                if (is_file(DIR_IMAGE . $image_new) && (!is_file(DIR_IMAGE . $image_new_webp) || (filemtime(DIR_IMAGE . $image_new) > filemtime(DIR_IMAGE . $image_new_webp)))) {
                    $path = '';
                    $directories = explode('/', dirname($image_new_webp));
                    foreach ($directories as $directory) {
                        $path = $path . '/' . $directory;

                        if (!is_dir(DIR_IMAGE . $path)) {
                            @mkdir(DIR_IMAGE . $path, 0755);
                        }
                    }
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
                if(is_file(DIR_IMAGE . $image_new_webp)) {
                    if(stripos($this->request->server['REQUEST_URI'], 'admin') === false && isset($this->request->server['HTTP_ACCEPT']) && strpos($this->request->server['HTTP_ACCEPT'], 'image/webp') !== false) {
                        $image_new = $image_new_webp;
                    }
                }
            }
        }
    }

    if ($this->request->server['HTTPS']) {
        return $this->config->get('config_ssl') . 'image/' . $image_new;
    } else {
        return $this->config->get('config_url') . 'image/' . $image_new;
    }
	}
}
