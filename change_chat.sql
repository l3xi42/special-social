-- email, preslo 21 dni
SELECT  * 
FROM group_users
WHERE timestamp < NOW()
UPDATE group_users
SET send_email = 0
-- email, potvrdenie
UPDATE group_users
SET send_email = 0
WHERE user_id = uid AND group_id = vkey


-- email, preslo 28 dni

-- automaticky ostava
SELECT * FROM group_users
WHERE send_email=0
-- send email
AND 
UPDATE group
SET key_id = :keyid, hobby = :hobby, level = :level, language=:language, place=:place
UPDATE group_users
SET user_id = :id, key_id = :keyid, hobby=:hobby

-- automaticky presunuty
SELECT * FROM group_users
WHERE send_email=1
-- send email
AND 
UPDATE group
SET key_id = :keyid, hobby = :hobby, level = :level, language=:language, place=:place
UPDATE group_users
SET user_id = :id, key_id = :keyid, hobby=:hobby

--vymaz skupinu ak 0 uzivatelov
WITH POC_UID AS
(   SELECT key_id,
COUNT(DISTINCT(user_id)) AS POCET_UID 
FROM `group_users`
GROUP BY key_id
)
DELETE
    G.*,
    POCET_UID
FROM `groups` G 
LEFT JOIN POC_UID
    ON POC_UID.key_id = G.key_id
WHERE G.hobby = :hobby
AND G.level = :level
AND G.language = :lang
AND G.place = :place
AND POCET_UID==0
ORDER BY POCET_UID ASC
LIMIT 1 
