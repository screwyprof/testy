SET time_zone = "+00:00";

CREATE TABLE `answers` (
  `ans_id` int(11) UNSIGNED NOT NULL,
  `ans_qst_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `ans_text` text NOT NULL,
  `ans_is_correct` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ;

INSERT INTO `answers` (`ans_id`, `ans_qst_id`, `ans_text`, `ans_is_correct`) VALUES
(1, 1, 'Koala', 1),
(2, 1, 'Panda', 0),
(3, 1, 'Groundhog', 0),
(4, 1, 'Kangaroo', 1),
(5, 2, 'Lion', 0),
(6, 2, 'Puma', 0),
(7, 2, 'Lynx', 0),
(8, 2, 'Wolf', 1),
(9, 3, 'cow', 1),
(10, 4, 'Egg', 4),
(11, 4, 'Adult Butterfly', 5),
(12, 4, 'Larva (Caterpillar)', 1),
(13, 4, 'Pupa (Chrysalis)', 2),
(14, 4, 'Young Caterpillar', 3),
(15, 5, 'giraffe', 1),
(16, 6, 'German Shepherd', 4),
(17, 6, 'Yorkshire Terrier', 1),
(18, 6, 'Scottish Terrier', 2),
(19, 6, 'Great Dane', 5),
(20, 6, 'Staffordshire Terrier', 3),
(21, 7, 'Cobra', 0),
(22, 7, 'Levantine Viper', 0),
(23, 7, 'Python', 1),
(24, 7, 'Adder', 0),
(25, 7, 'Smooth Snake', 0),
(26, 8, 'Cow', 0),
(27, 8, 'Horse', 0),
(28, 8, 'Zebra', 0),
(29, 8, 'Goat', 1),
(30, 8, 'Saiga Antelope', 1),
(31, 8, 'Bison', 0),
(32, 8, 'Deer', 1);

CREATE TABLE `questions` (
  `qst_id` int(11) UNSIGNED NOT NULL,
  `qst_test_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `qst_is_enabled` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `qst_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `qst_text` text NOT NULL
) ;

INSERT INTO `questions` (`qst_id`, `qst_test_id`, `qst_is_enabled`, `qst_type`, `qst_text`) VALUES
(1, 1, 1, 4, 'Which of the listed animals are marsupials?'),
(2, 1, 1, 3, 'Which of these animals did Jack London nickname &quot;White Fang&quot;?'),
(3, 1, 1, 1, 'Name the animal considered sacred in India...'),
(4, 1, 1, 2, 'Arrange the life cycle stages of a butterfly in correct order:'),
(5, 1, 1, 1, 'The tallest animal on Earth today'),
(6, 1, 1, 5, 'Arrange these dog breeds by size starting with the smallest'),
(7, 1, 1, 3, 'Mark the snake that, in your opinion, does not belong in this list'),
(8, 1, 1, 4, 'Which of these animals are even-toed ungulates?');

CREATE TABLE `results` (
  `rst_id` int(11) UNSIGNED NOT NULL,
  `rst_test_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `rst_usr_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `rst_start_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `rst_stop_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `rst_time_spent` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `rst_is_time_exceeded` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `rst_points` float UNSIGNED NOT NULL DEFAULT 0,
  `rst_mark` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ;

CREATE TABLE `results_answers` (
  `id` int(11) UNSIGNED NOT NULL,
  `rst_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `qst_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `ans_vr_order` text NOT NULL,
  `ans_correct` text NOT NULL,
  `ans_answer` text NOT NULL,
  `ans_percents` float UNSIGNED NOT NULL DEFAULT 0,
  `ans_is_correct` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ans_timespent` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `ans_is_time_exceeded` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ;

CREATE TABLE `tests` (
  `test_id` int(11) UNSIGNED NOT NULL,
  `test_is_enabled` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `test_start_time` int(11) UNSIGNED DEFAULT 0,
  `test_stop_time` int(11) UNSIGNED DEFAULT 0,
  `test_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `test_title` varchar(255) NOT NULL,
  `test_desc` text NOT NULL,
  `test_is_show_report` tinyint(1) UNSIGNED DEFAULT 1,
  `test_qst_show_cnt` tinyint(3) UNSIGNED NOT NULL DEFAULT 15,
  `test_is_mix_qst` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `test_is_mix_ans` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `test_is_show_answers` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `test_qst_per_page` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ;

INSERT INTO `tests` (`test_id`, `test_is_enabled`, `test_start_time`, `test_stop_time`, `test_time`, `test_title`, `test_desc`, `test_is_show_report`, `test_qst_show_cnt`, `test_is_mix_qst`, `test_is_mix_ans`, `test_is_show_answers`, `test_qst_per_page`) VALUES
(1, 1, 1577826000, 2524597200, 600, 'Animals', 'Test with interesting questions about animals. The test supports different question types', 1, 5, 1, 1, 0, 0);

CREATE TABLE `users` (
  `usr_id` int(11) UNSIGNED NOT NULL,
  `usr_login` varchar(32) NOT NULL,
  `usr_passwd` varchar(32) NOT NULL,
  `usr_firstname` varchar(64) NOT NULL,
  `usr_lastname` varchar(64) NOT NULL,
  `usr_thirdname` varchar(64) NOT NULL,
  `usr_email` varchar(255) NOT NULL,
  `usr_role` char(1) DEFAULT 'u',
  `usr_is_enabled` tinyint(1) UNSIGNED DEFAULT 1
) ;

INSERT INTO `users` (`usr_id`, `usr_login`, `usr_passwd`, `usr_firstname`, `usr_lastname`, `usr_thirdname`, `usr_email`, `usr_role`, `usr_is_enabled`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'John', 'Doe', 'Raymond', 'admin@example.com', 'a', 1),
(2, 'editor', '5aee9dbd2a188839105073571bee1b1f', 'Jane', 'Smith', 'Marie', 'editor@example.com', 'e', 1),
(3, 'student', '098f6bcd4621d373cade4e832627b4f6', 'Bob', 'Student', 'Alan', 'student@example.com', 'u', 1);


ALTER TABLE `answers`
  ADD PRIMARY KEY (`ans_id`),
  ADD KEY `ans_qst_id` (`ans_qst_id`);

ALTER TABLE `questions`
  ADD PRIMARY KEY (`qst_id`),
  ADD KEY `qst_test_id` (`qst_test_id`);

ALTER TABLE `results`
  ADD PRIMARY KEY (`rst_id`),
  ADD KEY `rst_test_id` (`rst_test_id`),
  ADD KEY `rst_usr_id` (`rst_usr_id`);

ALTER TABLE `results_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rst_id` (`rst_id`),
  ADD KEY `qst_id` (`qst_id`);

ALTER TABLE `tests`
  ADD PRIMARY KEY (`test_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`usr_id`),
  ADD UNIQUE KEY `usr_login` (`usr_login`);


ALTER TABLE `answers`
  MODIFY `ans_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `questions`
  MODIFY `qst_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `results`
  MODIFY `rst_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `results_answers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `tests`
  MODIFY `test_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `usr_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
