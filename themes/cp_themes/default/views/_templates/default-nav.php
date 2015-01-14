<?php extend_template('wrapper'); ?>

<?php if (isset($header)): ?>
	<div class="col-group">
		<div class="col w-16 last">
			<div class="box full mb">
				<div class="tbl-ctrls">
					<?php if (isset($header['form_url'])): ?>
						<?=form_open($header['form_url'])?>
							<fieldset class="tbl-search right">
								<input placeholder="<?=lang('type_phrase')?>" type="text" name="search" value="">
								<?php if (isset($header['search_button_value'])): ?>
								<input class="btn submit" type="submit" value="<?=$header['search_button_value']?>">
								<?php else: ?>
								<input class="btn submit" type="submit" value="<?=lang('search')?>">
								<?php endif; ?>
							</fieldset>
					<?php endif ?>
						<h1>
							<?=$header['title']?>
							<?php if (isset($header['toolbar_items']))
							{
								echo ee()->load->view('_shared/toolbar', $header, TRUE);
							} ?>
						</h1>
					<?php if (isset($header['form_url'])): ?>
						</form>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
<?php endif ?>

<div class="col-group">
	<?=$left_nav?>
	<div class="col w-12 last">
		<?php if (count($cp_breadcrumbs)): ?>
			<ul class="breadcrumb">
				<?php foreach ($cp_breadcrumbs as $link => $title): ?>
					<li><a href="<?=$link?>"><?=$title?></a></li>
				<?php endforeach ?>
				<li class="last"><?=$cp_page_title?></li>
			</ul>
		<?php endif ?>
		<?php if (enabled('outer_box')) :?>
			<div class="box">
		<?php endif ?>
			<?=$EE_rendered_view?>
		<?php if (enabled('outer_box')) :?>
			</div>
		<?php endif ?>
	</div>
</div>

<?php if (isset($blocks['modals'])) echo $blocks['modals']; ?>