const gulp = require('gulp');
const dist_name = 'hassle-free-date-list';

// 配布用ファイル書き出し
gulp.task('root', function () {
	return gulp
		.src([
			'*.php',
			'*.txt',
		])
		.pipe(gulp.dest(dist_name));
});

gulp.task('build-folder', function () {
	return gulp
		.src(['build/**'])
		.pipe(gulp.dest(dist_name + '/build'));
});

gulp.task('asset', function () {
	return gulp
		.src(['asset/**'], { base: 'asset' })
		.pipe(gulp.dest(dist_name + '/asset'));
});

gulp.task('includes', function () {
	return gulp
		.src(['includes/**'], { base: 'includes' })
		.pipe(gulp.dest(dist_name + '/includes'));
});

gulp.task('languages', function () {
	return gulp
		.src(['languages/**'], { base: 'languages' })
		.pipe(gulp.dest(dist_name + '/languages'));
});

gulp.task('src', function () {
	return gulp
		.src(['src/**'], { base: 'src' })
		.pipe(gulp.dest(dist_name + '/src'));
});

// 出力
gulp.task('dist', gulp.series('root', 'build-folder', 'asset', 'includes', 'languages', 'src'));
