import mysql.connector
from freud import Freud

# Jung searches for dreams and gives you answers
class Jung:
    def search(self, terms):
        f = Freud()
        tokens = f.process_sentence(terms)
        search_terms = []
        for token in tokens:
            search_terms.append(token[1])
        sql = """
            (
                SELECT
                    bin_to_uuid(dream_id) AS 'dream_id'
                FROM
                    freud.dream_word_freq dwf
                INNER JOIN
                    freud.word ON(
                        word.id = dwf.word_id
                    )
                WHERE
                    word.search LIKE %s
            )
        """
        first_term = search_terms.pop()
        params = [first_term + "%"]


        for search_term in search_terms:
            sql += "\nunion\n" + sql
            params.append(search_term + "%")

        print(sql)

        cnx = mysql.connector.connect(user='root', password='password', database='freud')
        cursor = cnx.cursor()
        cursor.execute(sql, params)
        for result in cursor:
            print(result)

j = Jung()
j.search("grandpa paul")
