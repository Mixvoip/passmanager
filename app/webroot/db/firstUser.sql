INSERT INTO users (id, username, email, salt, password, access_level, create_time, update_time, lastlogin, blocked)
VALUES (
  1, 'admin', '',
  '443dc75558b77917def50a5af37362bc9134a865351e5e4a9725ae6a0c14ffedafbd54baf435be81401a8955a4ad82ede022c0392c6292bfbd80326cf0c16f86',
  'bb8d7b04b52ecb97b463bf8d0cba4a761494b76af4036f7f0f5d2b321da00f1de4cb49e890646345e6d4c8bc290ccac1421b82368d28f22c5c23f5656c8e3c94',
  1, '', '', NULL, 0);

INSERT INTO tags (id, name, parenttag_id) VALUES (1, 'root', NULL);
INSERT INTO user_has_tags (id, user_id, tag_id) VALUES (NULL, 1, 1);

SELECT 'User admin and root tag created. His password is q1w2e3! . Please change it';
