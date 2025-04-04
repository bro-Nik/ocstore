<?php echo $header; ?>
<?php if((float)VERSION < 2) { ?>
<script   src="https://code.jquery.com/jquery-1.9.1.min.js"   integrity="sha256-wS9gmOZBqsqWxgIVgA8Y9WcQOa7PgSIX+rPA0VL2rbQ="   crossorigin="anonymous"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
<?php } ?>

        <div class="name">
            <h2>#SEO <p>Full IndeX</p></h2><sup>6 beta</sup></h2> 
        </div>
		
<div id="content">

    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right nopaddingtop">
                <button type="submit" form="form-fx" class="btn btn-primary" id="save"><i class="fa fa-check"></i></button>
                <a href="<?php echo $cancel; ?>" class="cancel btn btn-default"><i class="fa fa-times"></i></a>
            </div>
			
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">

        <div class="panel panel-default">
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-fx" class="form-horizontal">
                
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-info-circle"></i> Info</a></li>
						<?php if ($kley) { ?>
                        <li><a data-toggle="tab" href="#menu_canonical"><i class="fa fa-link"></i>  Canonical</a></li>
                        <li><a data-toggle="tab" href="#menu_meta"><i class="fa fa-code"></i>  Meta Tags / H1</a></li>
                        <li><a data-toggle="tab" href="#menu_redirect"><i class="fa fa-exchange"></i>  Redirect</a></li>
                        <!--li><a data-toggle="tab" href="#menu_xhr"><i class="fa fa-hourglass-o"></i>  XHR</a></li-->
                        <li><a data-toggle="tab" href="#menu_404"><i class="fa fa-times"></i>  404</a></li>
                        <li><a data-toggle="tab" href="#menu_content"><i class="fa fa-file-o"></i>  Content</a></li>						
                        <li><a data-toggle="tab" href="#menu_pagination"><i class="fa fa-files-o"></i>  Prev/next</a></li>
                        <li class="<?php if (!$fx_developer) {echo ' prehidden';} ?>"><a data-toggle="tab" href="#menu_developer"><i class="fa fa-exclamation-triangle"></i>  Developer</a></li>
                        <li class="<?php if (!$redirects_addon) {echo ' hidden';} ?>"><a data-toggle="tab" href="#menu4"><i class="fa fa-puzzle-piece"></i>  FX Redirects Manager</a></li>
						<?php } ?>
                    </ul>

                    <div class="tab-content">
                        <div id="home" class="fade out tab-pane in active">
						
							<h4 class="pull-right"><?=$ver?></h4>
		
		
                            <div class="errors col-sm-12">
								<?php if ($error_warning) { ?>
								<div class="alert alert-danger"><?php echo $error_warning; ?>
									<button type="button" class="close" data-dismiss="alert">&times;</button>
								</div>
								<?php } ?>
								<div class="alert alert-danger" style="display:none">
									<button type="button" class="close" data-dismiss="alert">&times;</button>
									<span></span>
								</div>
                            </div>							
						
                            <div class="form-group">
                                <div class="col-sm-6">
									<?php if (!$kley) { ?>
										<label class="col-sm-1 control-label">
											<i class="fa fa-key" aria-hidden="true"></i>
										</label>
										<div class="col-sm-4">
											<input type="text" name="kley" id="kley" value="<?php echo $kley; ?>" class="form-control" />
										</div>	
										<div id = "show_k"></div>
										<div class="col-sm-1">
											<input type="button" id="okkley" class="btn btn-default" value="ОК"/>
										</div>
									<?php } else { ?>
										<!--div><h3 class="col-sm-1"><i class="fa fa-key" aria-hidden="true"></i></h3><h4 class="col-sm-5">  <?php echo $kley; ?></h4></div-->
										<input type="text" name="kley" id="kley" value="<?php echo $kley; ?>" class="form-control hidden" />
									<?php } ?>
                                </div>

							</div>
							<div class="form-group delborder">

                                <div class="info">
									<p class="text-center"><img class="img-fluid" src="../image/catalog/fx_loading.gif"></p>
                                </div>

							</div>
							<div style="display:none">
								
								<textarea type="text" name="settings_text" id="settings_text" class="form-control" rows="15"></textarea>

								<input type="button" id="settings" class="btn btn-default" value="GO"/>
							</div>
							<div class="import form-group pull-right delborder">
								<div id="import" class="pull-right btn btn-warning" data-toggle="tooltip" title="Import settings from file (full-index.ru)">
								<i class="fa fa-cog"></i> Import settings 
								</div>
								<br>
								<!--div class="progress" style="display:none">
								  <div class="progress-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
								  <span class="sr-only">завершено 60%</span>
								  </div>
								</div-->
								<progress class="progress progress-striped progress-warning" style="display:none" value="0" max="100" aria-describedby="example-caption-1"><i class="fa fa-cog"></i> Import settings</progress>
								<br>								
							</div>

                        </div>
						
                        <div id="menu_pagination" class="fade out tab-pane<?php if (!$kley) { echo ' hidden';} ?>" >
                           
                            <div class="form-group delborder">



                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_google; ?>"><?php echo $entry_google; ?></span></label>

                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_google" name="fx_google" type="checkbox" value="<?php echo $fx_google ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_google"></label>	
                                </div>

								<div class="col-sm-12 form-group delborder"><br></div>


								<label class="col-sm-5 control-label"><?php echo $exceptions_GET; ?></label>
                                <div class="col-sm-7">
                                    <input type="text" name="fx_no_pagination" value="<?php echo $fx_no_pagination; ?>" id="fx_no_pagination" class="form-control" />
                                </div>																                                
								
								<div class="col-sm-12 form-group delborder"><br></div>
								
								<label class="col-sm-5 control-label"><?php echo $except_in_blank_pages; ?></label>
								<div class="col-sm-7 btn-group">
									<input class="tgl tgl-skewed" id="fx_no_blank_pages" name="fx_no_blank_pages" type="checkbox" value="<?php echo $fx_no_blank_pages ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_no_blank_pages"></label>	
                                </div>								


								
                            </div>

                        </div>	
						
                        <div id="menu_canonical" class="fade out tab-pane<?php if (!$kley) { echo ' hidden';} ?>" >

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="fx_canonicals"><span data-toggle="tooltip" title="<?php echo $help_canonicals; ?>"><?php echo $entry_canonicals; ?></span></label>
								
                                <label class="col-sm-3 control-label">category & etc...</label>
                                <div class="col-sm-7">
                                    <input type="text" name="fx_canonicals" value="<?php echo $fx_canonicals; ?>" id="fx_canonicals" class="form-control" />
                                    <span class="help-block"><?php echo $text_canonicals; ?></span>
                                </div>					
                                <label class="col-sm-2 control-label col-sm-offset-3">product</label>
                                <div class="col-sm-7">
                                    <input type="text" name="fx_canonicals_p" value="<?php echo $fx_canonicals_p; ?>" id="fx_canonicals_p" class="form-control" />
                                    <span class="help-block"><?php echo $text_canonicals; ?></span>
                                </div>					
                                <label class="col-sm-2 control-label col-sm-offset-3">home</label>
                                <div class="col-sm-7">
                                    <input type="text" name="fx_canonicals_h" value="<?php echo $fx_canonicals_h; ?>" id="fx_canonicals_h" class="form-control" />
                                    <span class="help-block"><?php echo $text_canonicals; ?></span>
                                </div>						
                                <label class="col-sm-2 control-label col-sm-offset-3">info</label>
                                <div class="col-sm-7">
                                    <input type="text" name="fx_canonicals_i" value="<?php echo $fx_canonicals_i; ?>" id="fx_canonicals_i" class="form-control" />
                                    <span class="help-block"><?php echo $text_canonicals; ?></span>
                                </div>
								
                             <label class="col-sm-2 control-label col-sm-offset-3"><?php echo $entry_description_canonical; ?></label>
                                <div class="col-sm-7 btn-group">
									<input class="tgl tgl-skewed" id="fx_description_canonical" name="fx_description_canonical" type="checkbox" value="<?php echo $fx_description_canonical ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_description_canonical"></label>	
                                </div>	
							</div>	
							
							<div class="form-group delborder">								
								<label class="col-sm-2 control-label"><?php echo $exceptions_GET; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="fx_no_canonical" value="<?php echo $fx_no_canonical; ?>" id="fx_no_canonical" class="form-control" />
                                    <span class="help-block"><?php echo $text_no_canonical; ?></span>
                                </div>
								
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_canonical_empty_page; ?>"><?php echo $entry_canonical_empty_page; ?></span></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_canonical_empty_page" name="fx_canonical_empty_page" type="checkbox" value="<?php echo $fx_canonical_empty_page ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_canonical_empty_page"></label>	
                                </div>								
                            </div>
							
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $text_max_page; ?></label>
                                <div class="col-sm-1">
                                    <input type="text" name="fx_max_page" value="<?php echo $fx_max_page; ?>" id="fx_max_page" class="form-control" size="3" maxlength="3"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_canonical_pattern; ?>"><?php echo $entry_canonical_pattern; ?></span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_canonical_pattern" value="<?php echo $fx_canonical_pattern; ?>" id="fx_canonical_pattern" class="form-control" />
                                    <span class="help-block"><?php echo $text_star; ?></span>
                                </div>
                            </div>				

                        </div>	

                        <div id="menu_meta" class="fade out tab-pane<?php if (!$kley) { echo ' hidden';} ?>" >

                            <div class="form-group delborder">
                                <h2 class="col-sm-1">Page > 1</h2>
                                <label class="col-sm-2 control-label" for="fx_title_pattern"><?php echo $entry_title_pattern; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_title_pattern" value="<?php echo $fx_title_pattern; ?>" id="fx_title_pattern" class="form-control" />
                                    <span class="help-block"><?php echo $text_title_pattern; ?></span>
                                </div>
                            </div>
							
                            <div class="form-group delborder">
                                <label class="col-sm-3 control-label" for="fx_metapattern"><span data-toggle="tooltip" title="<?php echo $help_metapattern; ?>"><?php echo $entry_metapattern; ?></span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_metapattern" value="<?php echo $fx_metapattern; ?>" id="fx_metapattern" class="form-control" />
                                    <span class="help-block"><?php echo $text_metapattern; ?></span>
                                </div>
                            </div>							
							
                            <div class="form-group delborder h11">
                                <label class="col-sm-3 control-label" for="fx_pattern"><span data-toggle="tooltip" title="<?php echo $help_pattern; ?>"><?php echo $entry_pattern; ?></span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_pattern" value="<?php echo $fx_pattern; ?>" id="fx_pattern" class="form-control" />
                                    <span class="help-block"><?php echo $text_pattern; ?></span>
                                </div>
                            </div>
							
                            <div class="form-group">
                                <h2 class="col-sm-1">Page 1</h2>
                                <label class="col-sm-2 control-label" for="fx_title_pattern_category"><?php echo $entry_title_pattern; ?> - Categories</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_title_pattern_category" value="<?php echo $fx_title_pattern_category; ?>" id="fx_title_pattern_category" class="form-control" />
                                </div>
                            </div>
							
                            <div class="form-group delborder">
                                <label class="col-sm-3 control-label" for="fx_title_pattern_product"><?php echo $entry_title_pattern; ?> - Products</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_title_pattern_product" value="<?php echo $fx_title_pattern_product; ?>" id="fx_title_pattern_product" class="form-control" />
                                </div>
                            </div>
							
                            <div class="form-group delborder">
                                <label class="col-sm-3 control-label" for="fx_title_pattern_brand"><?php echo $entry_title_pattern; ?> - Brands</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_title_pattern_brand" value="<?php echo $fx_title_pattern_brand; ?>" id="fx_title_pattern_brand" class="form-control" />
                                </div>
                            </div>
							
							
                            <div class="form-group delborder">
								<div class="col-sm-3"><br></div>
								<div class="col-sm-9"><hr></div>
                            </div>
							
                            <div class="form-group delborder">
                                <label class="col-sm-3 control-label" for="fx_metapattern_category"><?php echo $entry_metapattern; ?> - Categories</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_metapattern_category" value="<?php echo $fx_metapattern_category; ?>" id="fx_metapattern_category" class="form-control" />
                                </div>
                            </div>
							
                            <div class="form-group delborder">
                                <label class="col-sm-3 control-label" for="fx_metapattern_product"><?php echo $entry_metapattern; ?> - Products</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_metapattern_product" value="<?php echo $fx_metapattern_product; ?>" id="fx_metapattern_product" class="form-control" />
                                </div>
                            </div>	
							
                            <div class="form-group delborder">
                                <label class="col-sm-3 control-label" for="fx_metapattern_brand"><?php echo $entry_metapattern; ?> - Brands</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_metapattern_brand" value="<?php echo $fx_metapattern_brand; ?>" id="fx_metapattern_brand" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_page; ?>"><?php echo $entry_page; ?></span></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_page" name="fx_page" type="checkbox" value="<?php echo $fx_page ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_page"></label>	
                                </div>								
                            </div>

                            <div class="form-group delborder">
                                <label class="col-sm-3 control-label">Meta Title → name</label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_title_to_name" name="fx_title_to_name" type="checkbox" value="<?php echo $fx_title_to_name ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_title_to_name"></label>	
                                </div>								
                            </div>
							
							<div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_sortlimit; ?>"><?php echo $entry_sortlimit; ?></span></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_sortlimit" name="fx_sortlimit" type="checkbox" value="<?php echo $fx_sortlimit ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_sortlimit"></label>	
                                </div>
                            </div>

                        </div>


                        <div id="menu_redirect" class="fade out tab-pane<?php if (!$kley) { echo ' hidden';} ?>" >		

                            <div class="form-group delborder">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_301; ?>"><?php echo $entry_301; ?></span></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_301" name="fx_301" type="checkbox" value="<?php echo $fx_301 ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_301"></label>	
                                </div>									
                            </div>	

                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_review_301; ?>"><?php echo $entry_review_301; ?></span></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_review_301" name="fx_review_301" type="checkbox" value="<?php echo $fx_review_301 ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_review_301"></label>	
                                </div>									
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_seopro_tags; ?>"><?php echo $entry_seopro_tags; ?></span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_seopro_tags" value="<?php echo $fx_seopro_tags; ?>" id="fx_seopro_tags" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="fx_redirects"><span data-toggle="tooltip" title="<?php echo $help_redirects; ?>"><?php echo $entry_redirects; ?></span></label>

								<label class="col-sm-2 control-label">category & etc...</label>
                                <div class="col-sm-7">
                                    <input type="text" name="fx_redirects" value="<?php echo $fx_redirects; ?>" id="fx_redirects" class="form-control" />
                                    <span class="help-block"><?php echo $text_redirects; ?></span>
                                </div>								
                                <label class="col-sm-2 control-label col-sm-offset-3">product</label>
                                <div class="col-sm-7">
                                    <input type="text" name="fx_redirects_p" value="<?php echo $fx_redirects_p; ?>" id="fx_redirects_p" class="form-control" />
                                    <span class="help-block"><?php echo $text_redirects; ?></span>
                                </div>
                                <label class="col-sm-2 control-label col-sm-offset-3">home</label>
                                <div class="col-sm-7">
                                    <input type="text" name="fx_redirects_h" value="<?php echo $fx_redirects_h; ?>" id="fx_redirects_h" class="form-control" />
                                    <span class="help-block"><?php echo $text_redirects; ?></span>
                                </div>
                                <label class="col-sm-2 control-label col-sm-offset-3">info</label>
                                <div class="col-sm-7">
                                    <input type="text" name="fx_redirects_i" value="<?php echo $fx_redirects_i; ?>" id="fx_redirects_i" class="form-control" />
                                    <span class="help-block"><?php echo $text_redirects; ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_redirect_pattern; ?>"><?php echo $entry_redirect_pattern; ?></span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_redirect_pattern" value="<?php echo $fx_redirect_pattern; ?>" id="fx_redirect_pattern" class="form-control" />
                                    <span class="help-block"><?php echo $text_star; ?></span>
                                </div>
                            </div>
							

                            <div class="war form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_redirect_empty_page; ?></label>
                                <div class="col-sm-1">
                                    <input type="text" name="fx_redirect_empty_page" value="<?php echo $fx_redirect_empty_page; ?>" id="fx_redirect_empty_page" class="form-control" size="3" maxlength="3"/>
                                </div>
                            </div>

                        </div>
						
                        <div id="menu_xhr" class="fade out tab-pane<?php if (!$kley) { echo ' hidden';} ?>" >
                           


                        </div>							

                        <div id="menu_404" class="war tab-pane<?php if (!$kley) { echo ' hidden';} ?>" >
                           
                          	<div class="form-group delborder">								
								<label class="col-sm-2 control-label">404 GET</label>
                                <div class="col-sm-10">
                                    <input type="text" name="fx_404_get" value="<?php echo $fx_404_get; ?>" id="fx_404_get" class="form-control" />
                                </div>
								
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_404_empty_page; ?></label>								
                                <div class="col-sm-10 btn-group">

									<input class="tgl tgl-skewed" id="fx_404_empty_page" name="fx_404_empty_page" type="checkbox" value="<?php echo $fx_404_empty_page ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_404_empty_page"></label>	

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">404 Mask</label>
                                <div class="col-sm-10">
                                    <input type="text" name="fx_404_pattern" value="<?php echo $fx_404_pattern; ?>" id="fx_404_pattern" class="form-control" />
                                    <span class="help-block"><?php echo $text_star; ?></span>
                                </div>
                            </div>
	
                        </div>							

                        <div id="menu_content" class="fade out tab-pane<?php if (!$kley) { echo ' hidden';} ?>" >
						
						    <div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_description; ?>"><?php echo $entry_description; ?></span></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_description" name="fx_description" type="checkbox" value="<?php echo $fx_description ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_description"></label>	
                                </div>									
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_h1; ?>"><?php echo $entry_h1; ?></span></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_hide_h1" name="fx_hide_h1" type="checkbox" value="<?php echo $fx_hide_h1 ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_hide_h1"></label>	
                                </div>								
                            </div>

                            <div class="form-group delborder h11">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_span; ?>"><?php echo $entry_span; ?></span></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_span" name="fx_span" type="checkbox" value="<?php echo $fx_span ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_span"></label>	
                                </div>
                            </div>
							
                            <div class="form-group delborder h11">
                                <label class="col-sm-3 control-label" for="fx_style"><span data-toggle="tooltip" title="<?php echo $help_style; ?>"><?php echo $entry_style; ?></span></label>
                                <div class="col-sm-9">
                                    <textarea type="text" name="fx_style" id="fx_style" class="form-control" rows="6"><?php echo $fx_style; ?></textarea>
                                </div>
                            </div>
							
							<div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $clear_sort_limit_doubles; ?></label>
                                <div class="col-sm-2 btn-group">
									<input class="tgl tgl-skewed" id="fx_clear_sort_limit_doubles" name="fx_clear_sort_limit_doubles" type="checkbox" value="<?php echo $fx_clear_sort_limit_doubles ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_clear_sort_limit_doubles"></label>	
                                </div>
                                <label class="col-sm-1 control-label">sort</label>
                                <div class="col-sm-1">
                                    <input type="text" name="fx_clear_sort" value="<?php echo $fx_clear_sort; ?>" id="fx_clear_sort" class="form-control" size="2" />
                                </div>
                                <label class="col-sm-1 control-label">order</label>
                                <div class="col-sm-1">
                                    <input type="text" name="fx_clear_limit" value="<?php echo $fx_clear_limit; ?>" id="fx_clear_limit" class="form-control" size="2" />
                                </div>
                            </div>
							
							<div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_review_page; ?>"><?php echo $entry_review_page; ?></span></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_review_page" name="fx_review_page" type="checkbox" value="<?php echo $fx_review_page ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_review_page"></label>	
                                </div>
                            </div>
							
							<div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $text_tags_fix; ?></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_tags_fix" name="fx_tags_fix" type="checkbox" value="<?php echo $fx_tags_fix ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_tags_fix"></label>	
                                </div>
                            </div>
							
							<div class="form-group">							
								<select name="fx_layout" class="form-control">
								  <option value="0">---</option>
								  <?php foreach ($layouts as $layout) { ?>
								  <option value="<?php echo $layout['layout_id']; ?>"  <?php if ($fx_layout == $layout['layout_id']) echo 'selected="selected" ';  echo '>' . $layout['name']; ?></option>
								  <?php } ?>
								</select>
                            </div>
<script>

</script>
							
							<!--div class="form-group">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_fx_hide_cats_list; ?>"><?php echo $entry_fx_hide_cats_list; ?></span></label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_hide_cats_list" name="fx_hide_cats_list" type="checkbox" value="<?php echo $fx_hide_cats_list ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_hide_cats_list"></label>	
                                </div>
                            </div-->

                            <div class="form-group buttons">
                                <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_hide; ?>"><?php echo $entry_hide; ?></span></label>
								
                                <label class="col-sm-5 col-sm-offset-4 noindex_addon">&lt;!--noindex--&gt;</label>
								
                                <label class="col-sm-2 control-label"><?php echo $entry_fx_hide_cats_list; ?></label>
                                <div class="col-sm-2 btn-group">
									<input class="tgl tgl-skewed" id="fx_hide_cats_list" name="fx_hide_cats_list" type="checkbox" value="<?php echo $fx_hide_cats_list ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_hide_cats_list"></label>	
                                </div>
								<div class="col-sm-2 btn-group noindex_addon">
									<input class="tgl tgl-skewed" id="fx_noindex_addon_cats_list" name="fx_noindex_addon_cats_list" type="checkbox" value="<?php echo $fx_noindex_addon_cats_list ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_noindex_addon_cats_list"></label>	
                                </div>

								<label class="col-sm-2 control-label col-sm-offset-3"><?php echo $top; ?></label>
                                <div class="col-sm-2 btn-group">
									<input class="tgl tgl-skewed" id="fx_hide_content_top" name="fx_hide_content_top" type="checkbox" value="<?php echo $fx_hide_content_top ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_hide_content_top"></label>	
                                </div>
								<div class="col-sm-2 btn-group noindex_addon">
									<input class="tgl tgl-skewed" id="fx_noindex_addon_top" name="fx_noindex_addon_top" type="checkbox" value="<?php echo $fx_noindex_addon_top ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_noindex_addon_top"></label>	
                                </div>
								
                                <label class="col-sm-2 control-label col-sm-offset-3"><?php echo $left; ?></label>
                                <div class="col-sm-2 btn-group">
									<input class="tgl tgl-skewed" id="fx_hide_column_left" name="fx_hide_column_left" type="checkbox" value="<?php echo $fx_hide_column_left ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_hide_column_left"></label>	
                                </div>								
									<div class="col-sm-2 btn-group noindex_addon">
									<input class="tgl tgl-skewed" id="fx_noindex_addon_left" name="fx_noindex_addon_left" type="checkbox" value="<?php echo $fx_noindex_addon_left ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_noindex_addon_left"></label>	
                                </div>
								
                                <label class="col-sm-2 control-label col-sm-offset-3"><?php echo $right; ?></label>
                                <div class="col-sm-2 btn-group">
									<input class="tgl tgl-skewed" id="fx_hide_column_right" name="fx_hide_column_right" type="checkbox" value="<?php echo $fx_hide_column_right ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_hide_column_right"></label>	
                                </div>
								<div class="col-sm-2 btn-group noindex_addon">
									<input class="tgl tgl-skewed" id="fx_noindex_addon_right" name="fx_noindex_addon_right" type="checkbox" value="<?php echo $fx_noindex_addon_right ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_noindex_addon_right"></label>	
                                </div>
								
                                <label class="col-sm-2 control-label col-sm-offset-3"><?php echo $bottom; ?></label>
                                <div class="col-sm-2 btn-group">
									<input class="tgl tgl-skewed" id="fx_hide_content_bottom" name="fx_hide_content_bottom" type="checkbox" value="<?php echo $fx_hide_content_bottom ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_hide_content_bottom"></label>	
                                </div>
								<div class="col-sm-2 btn-group noindex_addon">
									<input class="tgl tgl-skewed" id="fx_noindex_addon_bottom" name="fx_noindex_addon_bottom" type="checkbox" value="<?php echo $fx_noindex_addon_bottom ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_noindex_addon_bottom"></label>	
                                </div>
								
                            </div>

                        </div>
						
	<iframe style="display:none" scrolling="no" src="../index.php?route=<?php if((float)VERSION >= 2.3) echo 'extension/'; ?>module/fx/info&data=<?php echo $fx_version; ?>" id="frame" onload="iframeLoaded();" sandbox="allow-popups"></iframe>
						
						<div id="menu_developer" class="fade out tab-pane<?php if (!$fx_developer || !$kley) {echo ' prehidden';} ?>">
						
						    <div class="form-group ramka">
                                <label class="col-sm-2 control-label">Module OFF Routes</label>
								<div class="col-sm-10">
									<input type="text" name="fx_routes_exclude" value="<?php echo $fx_routes_exclude; ?>" id="fx_routes_exclude" class="form-control" />
								</div>
								<hr>
                                <label class="col-sm-2 control-label">Module OFF GETs</label>
								<div class="col-sm-10">
									<input type="text" name="fx_gets_exclude" value="<?php echo $fx_gets_exclude; ?>" id="fx_gets_exclude" class="form-control" />
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo 'Activate in OCFilter pages'; ?></label>
									<div class="col-sm-10 btn-group">
										<input class="tgl tgl-skewed" id="fx_ocfilter" name="fx_ocfilter" type="checkbox" value="<?php echo $fx_ocfilter ? 1 : 0; ?>">
										<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_ocfilter"></label>
									</div>									
								</div>								
                            </div>
						
						    <div class="form-group delborder">
                                <label class="col-sm-3 control-label">block noindex</label>
                                <div class="col-sm-2 btn-group">
									<input class="tgl tgl-skewed" id="fx_block_noindex" name="fx_block_noindex" type="checkbox" value="<?php echo $fx_block_noindex ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_block_noindex"></label>
								</div>
								<label class="col-sm-2 control-label"><?php echo $exceptions_GET; ?></label>
								<div class="col-sm-5">
									<input type="text" name="fx_noindex_exceptions_get" value="<?php echo $fx_noindex_exceptions_get; ?>" id="fx_noindex_exceptions_get" class="form-control" />
								</div>					
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">noindex</label>
                                <div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_noindex" name="fx_noindex" type="checkbox" value="<?php echo $fx_noindex ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_noindex"></label>	
                                </div>										
                            </div>

                            <div class="form-group follow delborder war">
                                <label class="col-sm-3 control-label">noindex GET</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_noindex_get" value="<?php echo $fx_noindex_get; ?>" id="fx_noindex_get" class="form-control" />
                                </div>
                            </div>
							
                            <div class="form-group follow delborder war">
                                <label class="col-sm-3 control-label">noindex mask</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_noindex_mask" value="<?php echo $fx_noindex_mask; ?>" id="fx_noindex_mask" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group follow delborder war">
                                <label class="col-sm-3 control-label">&lt;meta name=</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_noindex_name" value="<?php echo $fx_noindex_name; ?>" id="fx_noindex_name" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group follow delborder" id="follow">
								<div class="col-sm-9 col-sm-offset-3  btn-group">
									<input class="tgl tgl-skewed" id="fx_follow" name="fx_follow" type="checkbox" value="<?php echo $fx_follow ? 1 : 0; ?>">
									<label class="tgl-btn tgl-btn6 long" data-tg-off="NoFollow" data-tg-on="Follow" for="fx_follow"></label>	
                                </div>								
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">GET include in canonical</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_canonical_paht" value="<?php echo $fx_canonical_paht; ?>" id="fx_canonical_paht" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group delborder">
                                <label class="col-sm-3 control-label">GET include in prev|next</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fx_prev_next_paht" value="<?php echo $fx_prev_next_paht; ?>" id="fx_prev_next_paht" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $canonical_cicle_error_protection; ?></label>
								<div class="col-sm-9 btn-group">
									<input class="tgl tgl-skewed" id="fx_canonical_protection" name="fx_canonical_protection" type="checkbox" value="<?php echo $fx_canonical_protection ? 1 : 0; ?>">
									<label class="tgl-btn" data-tg-off="" data-tg-on="✓" for="fx_canonical_protection"></label>	
                                </div>								
                            </div>
						
						</div>
						
						
						<div id="menu4" class="fade out tab-pane<?php if (!$redirects_addon) {echo ' hidden';} ?>">						
							<div class="form-group delborder">
								<label class="col-sm-2 control-label" for="fx_redirect_list"><?php echo $redirect_list; ?></label>
								<div class="col-sm-10">
									<textarea type="text" name="fx_redirect_list" id="fx_redirect_list" class="form-control" rows="12" readonly><?php echo $fx_redirect_list; ?></textarea>
									<span class="pull-right"><i id="redirect_list_edit" class="fa fa-pencil-square-o"></i></span>							
								</div>
							</div>
							<div class="form-group delborder">
								<div class="col-sm-5"><input type="text" id="from" class="form-control" /></div>
								<div class="col-sm-1"><span class="go">→</span></div>
								<div class="col-sm-5"><input type="text" id="to" class="form-control" /></div>
								
								<div class="col-sm-1"><div id="add" class="btn btn-primary"><i class="fa fa-arrow-circle-o-right"></i></div></div>
							</div>
							
						</div>


                    </div>
                </form>
						<div class="powered"><span>Powered by <a href="//www.full-index.ru/" target="_blank"><p>Full Inde</p><span>X</span>.ru</a></span></div>
            </div>
        </div>
    </div>
<script>
$(document).ready(function () {                            
    $(".yes").change(function () {
        if ($(".yes").prop("checked", true) ) {
            $('.follow').show();
			/*$(".yes").prop("disabled", true);*/
        }
    });
    $(".no").change(function () {
        if ($(".no").prop("checked", true) ) {
            $('.follow').hide();
        }
    });
    if ($(".no").is(":checked")) {
        $('.follow').hide();
    }
	
});

$('#redirect_list_edit').click(function(){
	$('#fx_redirect_list').prop("readonly", false);
});

$('#add').click(function(){
	if (($('#from').val() != '') && ($('#to').val() != '')){
		var quote = '\n'+$('#from').val()+'→'+$('#to').val();
		$('#fx_redirect_list').append(quote);
	}
});

$('#okkley').click(function() {
	var pre_url = <?php echo "'".$kley_url."'"; ?>;
	var kley_url = pre_url.replace('module/fx', 'module/fx/fix&kley=' + $('#kley').val());
	<?php if((float)VERSION >= 2.3) { ?> kley_url = kley_url.replace('route=module/', 'route=extension/module/'); <?php } ?>
	$.ajax({
		url: kley_url,
		dataType: 'text',
		success: function(json) {
			$('#okkley').val('...');
			setTimeout(function () {	
			}, 3000);
			
		},
	});
	$.ajax({
		url: "../index.php?route=<?php if((float)VERSION >= 2.3) { echo 'extension/'; } ?>module/fx/data&data="+ $('#kley').val(),
		success: function(json) {
		},
		complete: function(json) {
			location.reload();
		},
	});	
});

$(document).ready(function () {                        
    $(".yes1").change(function () {
        if ($(".yes1").prop("checked", true) ) {
            $('.h11').hide("slow");
        }
    });
    $(".no1").change(function () {
        if ($(".no1").prop("checked", true) ) {
            $('.h11').show("slow");
        }
    });
    if ($(".yes1").is(":checked")) {
        $('.h11').hide();
    }
	
	$.ajax({
		url: '../index.php?route=<?php if((float)VERSION >= 2.3) { echo 'extension/'; } ?>module/fx/test',
		dataType: 'text',
		success: function(data) {
			if (data) {
				$('.errors .alert span').html(data);
				$('.errors .alert').show();
			}
		},
		complete: function(data) {
		},
	});
	

<?php if((float)VERSION < 2) { ?>
	$('.nav-tabs a').click(function(){
		$(this).tab('show');
	});
	$('[data-toggle="tooltip"]').tooltip();
<?php } ?>

});


$("form#form-fx").submit(function(e) {
    var url = location.href;
    $.ajax({
		type: "POST",
		url: url,
		data: $("form#form-fx").serialize(),
		success: function(data){
		},
		beforeSend: function() {
			$('#save').html('<i class="fa fa-spinner fa-spin"></i>');
		},
		complete: function() {
			setTimeout(function () {
				$('#save').html('<i class="fa fa-check"></i>');
			$('#save').removeClass("not_saved");
			}, 2000);			
		},
	});
	e.preventDefault();
});

$('.nav-tabs a').click(function() {
    var link = document.querySelector("link[rel*='icon']") || document.createElement('link');
    link.type = 'image/x-icon';
    link.rel = 'shortcut icon';
    link.href = '../admin/fx/fx.ico';
    document.getElementsByTagName('head')[0].appendChild(link);
});

$('#import').click(function() {
	var pre_url = <?php echo "'".$kley_url."'"; ?>;
	var kley_url = pre_url.replace('module/fx', 'module/fx/settings');
	var percent = 0;
	<?php if((float)VERSION >= 2.3) { ?> kley_url = kley_url.replace('route=module/', 'route=extension/module/'); <?php } ?>
	$.ajax({
		url: kley_url,
		dataType: 'text',
		beforeSend: function() {
			$('#import').hide();
		},
		success: function(json) {
		
			var n = json.indexOf("not found");
			var timeout = 0;			
			
			if (n < 1) {				
				$('.progress').show();
				setInterval(function() {
					if (percent < 100) {
						percent++;
						$('.progress').attr('value', percent);
					}				
					if (percent > 99) return;
				}, 20);
				timeout = 2000;
			}
			setTimeout(function () {
				$('.import').html('<div id="import_info"  class="pull-right"></div>');
				$('#import_info').html(json);
				
				$('#save').hide();
			}, timeout);
			
		},
	});
});

$('#settings').click(function() { //
	var pre_url = <?php echo "'".$kley_url."'"; ?>;
	var kley_url = pre_url.replace('module/fx', 'module/fx/filesave');
	var info = $('#settings_text').val();
	<?php if((float)VERSION >= 2.3) { ?> kley_url = kley_url.replace('route=module/', 'route=extension/module/'); <?php } ?>
	$.ajax({
		url: kley_url,
		data: {d: info},
		type: 'POST',
		beforeSend: function() {
			
		},
		success: function(json) {
			
			$('#settings_text').val(json);
			
		},
	});
});

$('input').change(function() {
  $('#save').addClass("not_saved");
});

$('#content').on('click', '#reload', function() {
	document.location.reload(true);
});


$('h1 sup').click(function() {
	window.open('/', '_blank');
});

$('#fx_canonical_paht').change(function() {
  $(this).removeClass("war");
  if (($(this).val() != 'page') && ($(this).val().indexOf("page,") < 0)) $(this).addClass("war");
});


  if (($('#fx_canonical_paht').val() != 'page') && ($('#fx_canonical_paht').val().indexOf("page,") < 0)) $('#fx_canonical_paht').addClass("war");




$('#frame').appendTo('.info');

setTimeout(function () {
	$('.info p').html('<p class = "text-center" style="padding: 101px 0">Сonnecrion to full-index.ru failed... You can see info directly on <a href="http://full-index.ru/messages/?for=<?php echo $fx_version; ?>">official site</a></p>');
}, 20000);

function iframeLoaded() {
    $('.info #frame').height($('.info #frame .content').height());
	
	setTimeout(function () {
		var mydiv = $('.info #frame').contents().find(".info .content");
		var h     = mydiv.height();
		console.log(h);
	}, 1000);
	
	var iframe = $('#frame', parent.document.body);
	iframe.height($('.frame').height());
	
	$('.info p').hide();
    $('#frame').show("slow");
}

$("input.tgl").on('change', function() {
  if ($(this).is(':checked')) {
    $(this).attr('value', '1');
  } else {
    $(this).attr('value', '0');
  }
  
  $('#checkbox-value').text($('#checkbox1').val());
});

$('input.tgl[value="1"]').attr( 'checked', true );

$('#column-left').remove();
	
</script>
</div>
<style>

.powered{
  text-align: center;
  font-size: 1.1em;
  padding: .9em;  
}

<?php if((float)VERSION < 2) { ?>
.page-header{margin-top:0}
.pull-right{padding-top: 15px}

<?php } ?>
.powered a{color:#777; border-bottom: 1px dotted;}

<?php if (!$fx_noindex_addon){ ?>
.noindex_addon{visibility: hidden;}
<?php } ?>

.powered a:hover{text-decoration: none; border-bottom: 1px solid;}

.name{
	background: #444;
	padding: 15px;
	padding-bottom: 10px;
	color: #fff; 
    border-radius: 2px;
}

h1 p, h2 p, .powered a p {
    color: #fb9c37!important;
    display: inline;
    font-weight: 600;
}

sup{
    top: -1em;    font-size: medium;
}

.breadcrumb li:last-child a {
    color: #32bd48!important;
}

h1 span, h2 span, .powered a span{color:#FB5151}
h1, h2 {display: inline}
#redirect_list_edit{font-size:2em; cursor: pointer;}
.go{font-size:1.8em; text-align:center}
.alert-danger {
  background-color: #adf;
  border: none;
  font-size: 1.25em;
  color: #FFF; }
.delborder{border: none!important}
/*.btn-group > label{padding: 5px 10px;}
.btn-group > label:not(.active){opacity:.5!important;padding: 5px 10px;}*/
.col-sm-7 > .help-block {margin-bottom: 20px;}
.form-group + .form-group { border-top: 1px solid #ededed; }
.yes:hover{cursor: no-drop}
.form-group {
    padding-top: 15px;
    padding-bottom: 15px;
    margin-bottom: 0;
}
.buttons .btn {margin-bottom:5px}
.panel-body {
  padding: 0; 
  padding-top: 0;
background: #444
  }

.breadcrumb li:last-child a {
    color: #1e91cf;
}
.breadcrumb li a {
    color: #999999;
    font-size: 11px;
    padding: 0px;
    margin: 0px;
}


  .tab-content {
	background: #fff;
	padding: 10px;
    border: 1px solid #444;
  }
  .nav-tabs {
	margin-bottom: 0;
	background: #444;	
    border-bottom: none;
	background-image: url('data:image/png;base64,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');
    background-repeat: no-repeat;
}  
.nav {
	padding-left: 39px;
	
}

.panel-default {
	border-top: 0
}
.panel-left .nav span {
	padding-top: 5px;
	color: #0dca24;
	padding-bottom: 1px;
	border: 1px solid #ddd;
	background: #e244a7;
}

#header, #column-left, #footer, #menu {
  display: none;

}

.panel-body, .panel, .panel-default{
	filter: none!important;
}


.btn-group label.btn { 
  min-width: 43px;
}

  .form-horizontal .form-group {
margin-left: -4px;
margin-right: -4px;
    }
	
.noindex_addon .btn-group{
	min-height: 39px;
}

#import_info{
display: block;
margin: 0;
padding:8px 0;
}

#import{
display: block;
clear: both;
margin: 8px 0;
}

.import{
	min-height: 68px;
	padding-bottom:0;
}

.progress{
    background-color: #f38733;
	margin-bottom: 0;
}

input.war{
	background: #f99;
}

.war label.control-label{
	color: #f55;
}

.breadcrumb {
    background: none;
    padding-left: 0!important;
	display: block;
	margin-top: 5px;
	margin-bottom: 15px;
}

.page-header h1{
	margin-bottom: 0;	
}

input {	
    padding-left: 10px!important;
}
div.btn {
	padding-top: 6px;
}

.page-header {
    padding-bottom: 0;
    margin: 0;
    border-bottom: none;
}

#save{
	background: #283;
	border-color: #a4c5a6;
}


#save:focus, #save:hover{
	background: #76c983;
	border-color: #94b596;
}

.btn:focus {
    outline: none;
}

.not_saved{
	background: #e77!important;
	border-color: #d66!important;
}

#column-left + #content {
    margin: 0px;
}
#content {
    padding-top: 20px;
    transition: all 0.3s;
}

.tab-pane{
	transition: all .2s ease;
}

.table thead td span[data-toggle="tooltip"]:after, label.control-label span:after {
    color: #607D8B;
}

.form-control {
  background-color: #f5f5f5;
  border: 1px solid #f7f1f1;
  box-shadow: none;
}

.form-control:hover {
  background-color: #ccc;
  border: 1px solid #f7f1f1;
  box-shadow: none;
}


.1tgl-btn:hover{
    opacity: .75;
}

.tgl-skewed:checked + .tgl-btn:hover {
    background: #eb812f;
}
.tgl-skewed + .tgl-btn:hover {
    background: #777;
}

#save:hover, .not_saved:hover {
    background: #ec5!important;
  border: 1px solid #db4 !important;
}

.nav-tabs > li > a {
color: #fff;
margin-right: 0;
border: none!important;
border-radius: 0;	
padding: 9px 15px 12px;	
border-top: 1px solid #444!important;
}

.nav-tabs > li > a:hover {
border-top: 1px solid #fb9c37!important;
transition: border-top 999ms;
}

.nav-tabs > li:hover, .nav-tabs > li a:hover {
  color: #444;
}

.nav-tabs > li.active:hover, .nav-tabs > li.active a:hover {
  background: #fff!important;
  color: #444;
 transition: 999ms;
}

.nav-tabs > li.active a {
	border-top: 1px solid #fb9c37;
}

#column-left + #content {
    margin-left: 0 !important
}

.tgl {
  display: none;
}

.tgl + .tgl-btn {
  outline: 0;
  display: block;
  width: 2em;
  height: 2em;
  position: relative;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
}

.tgl + .tgl-btn6 {
  width: 6em;
}

.tgl + .tgl-btn:after, .tgl + .tgl-btn:before {
  position: relative;
  display: block;
  content: "";
  width: 50%;
  height: 100%;
}
.tgl + .tgl-btn:after {
  left: 0;
}
.tgl + .tgl-btn:before {
  display: none;
}
.tgl:checked + .tgl-btn:after {
  left: 50%;
}

.tgl-skewed + .tgl-btn {
  overflow: hidden;
  -webkit-backface-visibility: hidden;
          backface-visibility: hidden;
  /*-webkit-transition: all .2s ease;
  transition: all .2s ease;*/
  font-family: sans-serif;
  background: #888;
  border-radius: 2px;
}
.tgl-skewed + .tgl-btn:after, .tgl-skewed + .tgl-btn:before {
  display: inline-block;
  /*
  -webkit-transition: all .2s ease;
  transition: all .2s ease;*/
  width: 100%;
  text-align: center;
  position: absolute;
  line-height: 2em;
  font-weight: bold;
  color: #fff;
}
.tgl-skewed + .tgl-btn:after {
  left: 100%;
  content: attr(data-tg-on);
}
.tgl-skewed + .tgl-btn:before {
  left: 0;
  content: attr(data-tg-off);
}
.tgl-skewed + .tgl-btn:active {
  background: #888;
}
.tgl-skewed + .tgl-btn:active:before {
  left: -10%;
}
.tgl-skewed:checked + .tgl-btn {
  background: #fb913f;
}
.tgl-skewed:checked + .tgl-btn:before {
  left: -100%;
}
.tgl-skewed:checked + .tgl-btn:after {
  left: 0;
}
.tgl-skewed:checked + .tgl-btn:active:after {
  left: 10%;
}

#container {
    background: #f9f9f9;    
	position: absolute;
    top: 0;
}

pre{
	margin: 0;
	background: #cde
}

.ramka{
	background: #ffc;
	margin-bottom: 30px
}
.close {
    opacity: 0.5;
	color: #fff
}

.panel-body label {
	white-space: nowrap;
	overflow: hidden
}

iframe {
	width: 650px;
	border: none;
	min-height: 450px;
}

.info{
    text-align: center;
}

.breadcrumb li:before {
    padding-left: 1px!important;
}
.breadcrumb li+li:before {
	content: '🞄';
}

.cancel{
    background: #444;
	color: #fff
}

</style>


<?php echo $footer; ?>