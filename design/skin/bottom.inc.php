<?php
/**
 * 서브페이지 디자인
 */
if(isset($gen)) {
?>

				<?php
				// 페이지하단 콘텐츠
				$bottom_content = $gen->bottom_content();
				if($bottom_content) echo '<div class="mb20">'.$bottom_content.'</div>';
				?>
			</div><!-- p_content_box End -->
			<div class="clear"></div>
		</div><!-- contents End -->
		<div class="clear"></div>
	</div><!-- content_wrap End -->

<?php
// 퀵배너 출력
$quick_banner = $rankup_control->print_banner('quick');
if($quick_banner) {
?>
	<div id="quick_banner">
		<?=$quick_banner?>
	</div>
	<script type="text/javascript"> quick_banner.initialize('content_wrap', 'quick_banner') </script>
<?php
}
?>

<?php
} // end of if(isset($gen))
?>