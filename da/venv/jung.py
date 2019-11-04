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

        # Build unions for query to get all dreams containing a search term
        sql = """
    (
        SELECT
           dream_id, dwf.word_id, dwf.frequency
        FROM
            freud.dream_word_freq dwf
        INNER JOIN
            freud.word ON(
                word.id = dwf.word_id
            )
        WHERE
            word.search LIKE %s
    )"""
        first_term = search_terms.pop()
        params = [first_term + "%"]

        for search_term in search_terms:
            sql += "\n\n\tunion\n" + sql
            params.append(search_term + "%")

        # Add in the main query to work on unioned data
        sql = "(" + sql + "\n)";
        sql = """
SELECT
    bin_to_uuid(z.dream_id) AS dream_id,
    SUM(z.frequency) AS total_frequency
FROM
""" + sql + """ z
GROUP BY
    dream_id
ORDER BY
    total_frequency DESC
        """

        print(sql)

        cnx = mysql.connector.connect(user='root', password='password', database='freud')
        cursor = cnx.cursor()
        cursor.execute(sql, params)
        for result in cursor:
            print(result)

j = Jung()
j.search("grandpa paul")
