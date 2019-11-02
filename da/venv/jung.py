import nltk
from nltk.tokenize import sent_tokenize
from nltk.tokenize import word_tokenize
from nltk.probability import FreqDist
from nltk.corpus import stopwords
from nltk.stem import PorterStemmer
from nltk.stem.wordnet import WordNetLemmatizer
from nltk.corpus import wordnet
import mysql.connector

# Converts treebank to wordnet format for parts of speech
def get_wordnet_pos(treebank_tag):
    if treebank_tag.startswith('J'):
        return wordnet.ADJ
    elif treebank_tag.startswith('V'):
        return wordnet.VERB
    elif treebank_tag.startswith('N'):
        return wordnet.NOUN
    elif treebank_tag.startswith('R'):
        return wordnet.ADV
    else:
        # Default to noun
        return wordnet.NOUN

# Processes a sentence into lemmatized tokens
def process_sentence(s, stop_words, lem, stem):
    # Break into words
    words = word_tokenize(s)

    # Get parts of speech
    pos = nltk.pos_tag(words)

    # Filter out things we don't want
    filtered_pos = []
    for p in pos:
        # Words at this point should all be lowercase
        word = p[0].lower()

        # Strip out prepositions, stop words, and anything starting with a single qoute
        # (seocnd half of contractions like coulodn't or would've)
        if not p[1].startswith('PRP') and word not in stop_words and not word[0] == "'":
            filtered_pos.append((word, get_wordnet_pos(p[1])))

    # Lemmatize
    if lem:
        lemmatized_words = []
        for p in filtered_pos:
            lemmatized_word = lem.lemmatize(p[0], p[1])
            lemmatized_word_verb = lem.lemmatize(p[0], 'v')

            # If the verb is shorter, we want that to fix issues with "swimming" being seen
            # as a noun and not lemmatized as "swim"
            if len(lemmatized_word_verb) < len(lemmatized_word):
                lemmatized_word = lemmatized_word_verb

            lemmatized_words.append(lem.lemmatize(p[0], p[1]))
        filtered_pos = lemmatized_words

    if stem:
        stemmatized_words = []
        for p in filtered_pos:
            stemmatized_words.append(stem.stem(p[0]))
        return stemmatized_words
    else:
        unfiltered_words = []
        for p in filtered_pos:
            unfiltered_words.append(p[0])
        return unfiltered_words

    return lemmatized_words

def save_word_frequency(dream_id, word, frequency):
    print(word + "/" + str(frequency))
    word_cursor = cnx.cursor()
    word_query = (" select id from word where word = %s ")
    word_cursor.execute(word_query, (word,))
    word_result = word_cursor.fetchone()
    word_cursor.close()
    # Word exists, use id
    if word_result:
        word_id = word_result[0]
    # Word does not exist, create and use id
    else:
        word_insert = ("""
            INSERT INTO word(word) VALUES( %s )
        """)
        insert_cursor = cnx.cursor()
        insert_cursor.execute(word_insert, (word,))
        word_id = insert_cursor.getlastrowid()
        insert_cursor.close()

    if word_id:
        word_freq_insert = ("""
            INSERT INTO dream_word_freq(dream_id, word_id, frequency) VALUES(uuid_to_bin( %s ), %s, %s)
        """)
        freq_insert_cursor = cnx.cursor()
        freq_insert_cursor.execute(word_freq_insert, (dream_id, word_id, frequency))
        freq_insert_cursor.close()
    else:
        print('Did not find word id for %s.', (word))


def preprocess_dream_text(text):
    # Convert weird unicode things to spaces
    text = text.replace('—', ' ')
    return text

def process_dream(dream_id, text, stop_words, lem, stem):
    print("Processing dream " + dream_id)
    dream_tokens = []
    # Split into sentences
    sentences = sent_tokenize(text)
    for s in sentences:
        dream_tokens.extend(process_sentence(s, stop_words, lem, stem))

    # Get sorted frequency of words
    for item in FreqDist(dream_tokens).items():
        save_word_frequency(dream_id, item[0], item[1] / len(dream_tokens))
    print("Finished.\n")

#
# Set Up
#

# Stop words
stop_words = stopwords.words("english")
stop_words.extend([",", ".", "!", "?", ";", ":", "n't", "’", "'", "\"", "”", "“"])

# Lemmatizer
lem = WordNetLemmatizer()
# lem = None

# Stemmer
stem = PorterStemmer()
# stem = None

# Connect to MySQL
cnx = mysql.connector.connect(user='root', password='password', database='freud')

# Get and process dreams
cursor = cnx.cursor(buffered=True)
dream_query = ("""
    select 
        bin_to_uuid(id) as 'id',
        concat(title, '. ', description) as 'text'
    from
        dj.dream
    where
        not exists(
            select
                1
            from
                freud.dream_word_freq dwf
            where
                dwf.dream_id = dream.id
            limit
                1
        )
    ;
""")

cursor.execute(dream_query)
for (id, text) in cursor:
    text = preprocess_dream_text(text)
    process_dream(id, text, stop_words, lem, stem)

cursor.close()
cnx.commit()
cnx.close()