--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.5
-- Dumped by pg_dump version 9.5.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

SET search_path = tag_engine_schema, pg_catalog;

ALTER TABLE ONLY tag_engine_schema.playlist_tag DROP CONSTRAINT "Foreign key for tag";
ALTER TABLE ONLY tag_engine_schema.playlist_tag DROP CONSTRAINT "Foreign key for playlist";
ALTER TABLE ONLY tag_engine_schema."user" DROP CONSTRAINT user_pkey;
ALTER TABLE ONLY tag_engine_schema."user" DROP CONSTRAINT user_key_key;
ALTER TABLE ONLY tag_engine_schema.tag_type DROP CONSTRAINT tag_type_title_key;
ALTER TABLE ONLY tag_engine_schema.tag_type DROP CONSTRAINT tag_type_pkey;
ALTER TABLE ONLY tag_engine_schema.tag DROP CONSTRAINT tag_title_key;
ALTER TABLE ONLY tag_engine_schema.tag DROP CONSTRAINT tag_pkey;
ALTER TABLE ONLY tag_engine_schema.playlist DROP CONSTRAINT playlist_pkey;
ALTER TABLE ONLY tag_engine_schema.playlist_tag DROP CONSTRAINT "one-to-one playlist and tag";
ALTER TABLE tag_engine_schema."user" ALTER COLUMN id DROP DEFAULT;
ALTER TABLE tag_engine_schema.tag_type ALTER COLUMN id DROP DEFAULT;
ALTER TABLE tag_engine_schema.tag ALTER COLUMN id DROP DEFAULT;
ALTER TABLE tag_engine_schema.playlist ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE tag_engine_schema.user_id_seq;
DROP TABLE tag_engine_schema."user";
DROP SEQUENCE tag_engine_schema.tag_type_id_seq;
DROP TABLE tag_engine_schema.tag_type;
DROP SEQUENCE tag_engine_schema.tag_id_seq;
DROP TABLE tag_engine_schema.tag;
DROP TABLE tag_engine_schema.playlist_tag;
DROP SEQUENCE tag_engine_schema.playlist_id_seq;
DROP TABLE tag_engine_schema.playlist;
DROP EXTENSION plpgsql;
DROP SCHEMA tag_engine_schema;
DROP SCHEMA public;
--
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- Name: tag_engine_schema; Type: SCHEMA; Schema: -; Owner: tag
--

CREATE SCHEMA tag_engine_schema;


ALTER SCHEMA tag_engine_schema OWNER TO tag;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = tag_engine_schema, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: playlist; Type: TABLE; Schema: tag_engine_schema; Owner: tag
--

CREATE TABLE playlist (
    title text,
    track_ids integer[],
    last_played timestamp without time zone DEFAULT now(),
    id integer NOT NULL,
    play_count integer,
    like_count integer,
    is_active boolean DEFAULT true,
    search_tags text
);


ALTER TABLE playlist OWNER TO tag;

--
-- Name: COLUMN playlist.search_tags; Type: COMMENT; Schema: tag_engine_schema; Owner: tag
--

COMMENT ON COLUMN playlist.search_tags IS 'Contains title of all tags related to this playlist separated by comma';


--
-- Name: playlist_id_seq; Type: SEQUENCE; Schema: tag_engine_schema; Owner: tag
--

CREATE SEQUENCE playlist_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE playlist_id_seq OWNER TO tag;

--
-- Name: playlist_id_seq; Type: SEQUENCE OWNED BY; Schema: tag_engine_schema; Owner: tag
--

ALTER SEQUENCE playlist_id_seq OWNED BY playlist.id;


--
-- Name: playlist_tag; Type: TABLE; Schema: tag_engine_schema; Owner: tag
--

CREATE TABLE playlist_tag (
    playlist_id integer NOT NULL,
    tag_id integer NOT NULL,
    is_active boolean DEFAULT true,
    created_on timestamp without time zone DEFAULT now()
);


ALTER TABLE playlist_tag OWNER TO tag;

--
-- Name: tag; Type: TABLE; Schema: tag_engine_schema; Owner: tag
--

CREATE TABLE tag (
    id integer NOT NULL,
    title text NOT NULL,
    last_used timestamp without time zone DEFAULT now(),
    type_id integer DEFAULT 4,
    is_active boolean DEFAULT true
);


ALTER TABLE tag OWNER TO tag;

--
-- Name: tag_id_seq; Type: SEQUENCE; Schema: tag_engine_schema; Owner: tag
--

CREATE SEQUENCE tag_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tag_id_seq OWNER TO tag;

--
-- Name: tag_id_seq; Type: SEQUENCE OWNED BY; Schema: tag_engine_schema; Owner: tag
--

ALTER SEQUENCE tag_id_seq OWNED BY tag.id;


--
-- Name: tag_type; Type: TABLE; Schema: tag_engine_schema; Owner: tag
--

CREATE TABLE tag_type (
    id integer NOT NULL,
    title text,
    weight integer DEFAULT 0 NOT NULL
);


ALTER TABLE tag_type OWNER TO tag;

--
-- Name: tag_type_id_seq; Type: SEQUENCE; Schema: tag_engine_schema; Owner: tag
--

CREATE SEQUENCE tag_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tag_type_id_seq OWNER TO tag;

--
-- Name: tag_type_id_seq; Type: SEQUENCE OWNED BY; Schema: tag_engine_schema; Owner: tag
--

ALTER SEQUENCE tag_type_id_seq OWNED BY tag_type.id;


--
-- Name: user; Type: TABLE; Schema: tag_engine_schema; Owner: tag
--

CREATE TABLE "user" (
    id integer NOT NULL,
    key text,
    last_accessed timestamp without time zone DEFAULT now()
);


ALTER TABLE "user" OWNER TO tag;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: tag_engine_schema; Owner: tag
--

CREATE SEQUENCE user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_id_seq OWNER TO tag;

--
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: tag_engine_schema; Owner: tag
--

ALTER SEQUENCE user_id_seq OWNED BY "user".id;


--
-- Name: id; Type: DEFAULT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY playlist ALTER COLUMN id SET DEFAULT nextval('playlist_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY tag ALTER COLUMN id SET DEFAULT nextval('tag_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY tag_type ALTER COLUMN id SET DEFAULT nextval('tag_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY "user" ALTER COLUMN id SET DEFAULT nextval('user_id_seq'::regclass);


--
-- Data for Name: playlist; Type: TABLE DATA; Schema: tag_engine_schema; Owner: tag
--

COPY playlist (title, track_ids, last_played, id, play_count, like_count, is_active, search_tags) FROM stdin;
Pop	{3,8}	2017-02-16 04:53:26.451516	7	1656	456	t	\N
Friday Jam Demo	{1,5,7}	2017-02-17 06:42:34.686726	17	2132	345	f	punjabi,bhangra,eminem,diljit dosanjh,ar rehman,bohemia
Punjabi	{1,5,7}	2017-02-16 20:59:49.207467	1	35679	1689	t	ar rehman,rock
Friday Jam	{1,5,7}	2017-02-17 06:40:28.047276	16	35679	1689	t	punjabi,diljit dosanjh,eminem,ar rehman,bohemia,bhangra
Rap God	{1,5}	2017-02-16 04:31:29.954053	2	50	40	t	punjabi,eminem,bohemia
Midnight	{3,5}	2017-02-16 04:34:39.640988	3	783	350	t	punjabi,bhangra,diljit dosanjh,chill,lady gaga
Blues	{3}	2017-02-16 04:35:57.643547	4	421	153	t	punjabi,bollywood,sad
Work Hard, Play Hard	{4,6}	2017-02-16 04:37:36.442702	5	1656	592	t	chill,pop,rock,lady gaga,bhangra,eminem
Chimp	{3,7,8}	2017-02-16 04:52:50.157834	6	165	25	t	eminem,coldplay,ar rehman
Everest	{3,6}	2017-02-16 04:54:35.336673	8	1651	256	t	bhangra,coldplay,instrumental,meditation,happy
Nighty	{6,8}	2017-02-16 04:57:07.164229	9	4666	1500	t	bollywood,coldplay,ar rehman,bohemia,bhangra,lady gaga
The Album	{234,6}	2017-02-16 04:58:09.967273	10	656	15	t	pop,rock,sad,happy
Bollywood Instru	{234,22}	2017-02-16 05:00:01.121588	11	1566	157	t	bollywood,meditation,focus,instrumental
Zip Zop	{1,5,7}	2017-02-16 16:37:33.456139	12	2132	345	t	punjabi,diljit dosanjh,eminem,ar rehman,bohemia,bhangra
Zip Zop2	{1,5,7}	2017-02-16 20:26:28.858486	13	2132	345	t	punjabi,diljit dosanjh,eminem,ar rehman,bohemia,bhangra
Zip Zop2	{1,5,7}	2017-02-16 20:26:59.607768	14	2132	345	t	punjabi,diljit dosanjh,eminem,ar rehman,bohemia,bhangra
\.


--
-- Name: playlist_id_seq; Type: SEQUENCE SET; Schema: tag_engine_schema; Owner: tag
--

SELECT pg_catalog.setval('playlist_id_seq', 17, true);


--
-- Data for Name: playlist_tag; Type: TABLE DATA; Schema: tag_engine_schema; Owner: tag
--

COPY playlist_tag (playlist_id, tag_id, is_active, created_on) FROM stdin;
17	1	f	2017-02-17 06:42:34.688891
17	5	f	2017-02-17 06:42:34.688891
17	4	f	2017-02-17 06:42:34.688891
2	4	t	2017-02-16 19:12:35.671336
2	8	t	2017-02-16 19:12:42.642061
2	1	t	2017-02-16 19:12:46.808268
3	1	t	2017-02-16 19:12:58.878382
3	2	t	2017-02-16 19:13:05.618749
3	5	t	2017-02-16 19:13:09.568227
3	15	t	2017-02-16 19:13:20.270801
4	13	t	2017-02-16 19:13:39.912067
4	3	t	2017-02-16 19:13:43.359133
4	1	t	2017-02-16 19:13:46.526198
5	15	t	2017-02-16 19:13:58.555508
5	10	t	2017-02-16 19:14:02.718887
5	12	t	2017-02-16 19:14:08.917238
5	9	t	2017-02-16 19:14:15.608973
5	2	t	2017-02-16 19:14:19.677116
5	4	t	2017-02-16 19:14:27.820458
6	4	t	2017-02-16 19:14:36.350619
6	6	t	2017-02-16 19:14:44.624282
6	7	t	2017-02-16 19:14:52.634975
3	9	t	2017-02-16 19:15:01.622165
9	3	t	2017-02-16 19:15:19.280192
8	2	t	2017-02-16 19:15:42.946968
8	6	t	2017-02-16 19:15:50.600217
8	18	t	2017-02-16 19:15:58.35065
8	16	t	2017-02-16 19:16:08.551987
8	14	t	2017-02-16 19:16:16.463794
17	7	f	2017-02-17 06:42:34.688891
9	6	t	2017-02-16 19:16:34.159033
9	7	t	2017-02-16 19:16:43.959965
9	8	t	2017-02-16 19:16:51.332059
9	2	t	2017-02-16 19:16:59.485589
9	9	t	2017-02-16 19:17:08.078884
10	10	t	2017-02-16 19:17:24.104363
10	12	t	2017-02-16 19:17:30.614431
10	14	t	2017-02-16 19:17:38.461222
10	13	t	2017-02-16 19:17:45.667834
11	18	t	2017-02-16 19:18:00.036501
11	16	t	2017-02-16 19:18:07.322921
11	17	t	2017-02-16 19:18:12.370875
11	3	t	2017-02-16 19:18:15.857551
12	1	t	2017-02-16 19:18:22.736452
12	5	t	2017-02-16 19:18:28.517009
12	4	t	2017-02-16 19:18:34.099807
12	7	t	2017-02-16 19:18:40.791623
12	8	t	2017-02-16 19:18:47.691576
12	2	t	2017-02-16 19:18:55.987137
13	1	t	2017-02-16 20:26:28.860571
13	5	t	2017-02-16 20:26:28.860571
13	4	t	2017-02-16 20:26:28.860571
13	7	t	2017-02-16 20:26:28.860571
13	8	t	2017-02-16 20:26:28.860571
13	2	t	2017-02-16 20:26:28.860571
14	1	t	2017-02-16 20:26:59.609227
14	5	t	2017-02-16 20:26:59.609227
14	4	t	2017-02-16 20:26:59.609227
14	7	t	2017-02-16 20:26:59.609227
14	8	t	2017-02-16 20:26:59.609227
14	2	t	2017-02-16 20:26:59.609227
17	8	f	2017-02-17 06:42:34.688891
17	2	f	2017-02-17 06:42:34.688891
1	4	f	2017-02-16 22:55:07.424244
1	8	f	2017-02-16 22:55:07.424244
1	2	f	2017-02-16 22:55:07.424244
1	7	t	2017-02-16 22:55:07.424244
1	12	t	2017-02-16 22:56:04.114874
16	12	f	2017-02-17 07:06:37.622421
16	1	t	2017-02-17 06:40:28.049191
16	5	t	2017-02-17 06:40:28.049191
16	4	t	2017-02-17 06:40:28.049191
16	7	t	2017-02-17 06:40:28.049191
16	8	t	2017-02-17 06:40:28.049191
16	2	t	2017-02-17 06:40:28.049191
\.


--
-- Data for Name: tag; Type: TABLE DATA; Schema: tag_engine_schema; Owner: tag
--

COPY tag (id, title, last_used, type_id, is_active) FROM stdin;
1	Punjabi	2017-02-15 19:45:07.805448	5	t
2	Bhangra	2017-02-15 19:45:39.165115	2	t
3	Bollywood	2017-02-15 19:46:59.453837	5	t
4	Eminem	2017-02-15 19:47:14.654127	1	t
5	Diljit Dosanjh	2017-02-15 19:52:39.161927	1	t
6	Coldplay	2017-02-15 19:52:59.40767	1	t
7	AR Rehman	2017-02-15 19:53:27.501164	1	t
8	Bohemia	2017-02-15 19:55:16.468416	1	t
9	Lady Gaga	2017-02-15 19:55:31.083371	1	t
10	Pop	2017-02-15 19:56:08.866907	2	t
12	Rock	2017-02-15 19:57:06.127796	2	t
13	Sad	2017-02-15 19:57:12.766837	3	t
14	Happy	2017-02-15 19:57:20.870647	3	t
15	Chill	2017-02-15 19:57:31.038698	3	t
16	Meditation	2017-02-15 19:57:38.780659	3	t
17	Focus	2017-02-15 19:57:44.069944	3	t
44	French	2017-02-17 06:27:41.050165	5	t
45	Russian	2017-02-17 06:27:52.272558	5	t
46	Tamil	2017-02-17 06:28:00.745952	5	t
47	Marathi	2017-02-17 06:28:06.786282	5	t
48	Bhojpuri	2017-02-17 06:28:17.397589	5	t
49	Rock and roll	2017-02-17 06:28:45.736549	2	t
50	Arabic	2017-02-17 06:37:17.149443	5	t
51	Arabicc	2017-02-17 06:37:30.772737	5	f
18	Instrumental	2017-02-15 19:58:21.831355	2	t
\.


--
-- Name: tag_id_seq; Type: SEQUENCE SET; Schema: tag_engine_schema; Owner: tag
--

SELECT pg_catalog.setval('tag_id_seq', 51, true);


--
-- Data for Name: tag_type; Type: TABLE DATA; Schema: tag_engine_schema; Owner: tag
--

COPY tag_type (id, title, weight) FROM stdin;
1	Artist	4
2	Genre	3
3	Mood	2
4	Misc	0
5	Regional	3
\.


--
-- Name: tag_type_id_seq; Type: SEQUENCE SET; Schema: tag_engine_schema; Owner: tag
--

SELECT pg_catalog.setval('tag_type_id_seq', 5, true);


--
-- Data for Name: user; Type: TABLE DATA; Schema: tag_engine_schema; Owner: tag
--

COPY "user" (id, key, last_accessed) FROM stdin;
1	thereisnosecretingredient	2017-02-15 18:52:09.629207
\.


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: tag_engine_schema; Owner: tag
--

SELECT pg_catalog.setval('user_id_seq', 1, true);


--
-- Name: one-to-one playlist and tag; Type: CONSTRAINT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY playlist_tag
    ADD CONSTRAINT "one-to-one playlist and tag" PRIMARY KEY (playlist_id, tag_id);


--
-- Name: playlist_pkey; Type: CONSTRAINT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY playlist
    ADD CONSTRAINT playlist_pkey PRIMARY KEY (id);


--
-- Name: tag_pkey; Type: CONSTRAINT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_pkey PRIMARY KEY (id);


--
-- Name: tag_title_key; Type: CONSTRAINT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_title_key UNIQUE (title);


--
-- Name: tag_type_pkey; Type: CONSTRAINT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY tag_type
    ADD CONSTRAINT tag_type_pkey PRIMARY KEY (id);


--
-- Name: tag_type_title_key; Type: CONSTRAINT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY tag_type
    ADD CONSTRAINT tag_type_title_key UNIQUE (title);


--
-- Name: user_key_key; Type: CONSTRAINT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_key_key UNIQUE (key);


--
-- Name: user_pkey; Type: CONSTRAINT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: Foreign key for playlist; Type: FK CONSTRAINT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY playlist_tag
    ADD CONSTRAINT "Foreign key for playlist" FOREIGN KEY (playlist_id) REFERENCES playlist(id);


--
-- Name: Foreign key for tag; Type: FK CONSTRAINT; Schema: tag_engine_schema; Owner: tag
--

ALTER TABLE ONLY playlist_tag
    ADD CONSTRAINT "Foreign key for tag" FOREIGN KEY (tag_id) REFERENCES tag(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

