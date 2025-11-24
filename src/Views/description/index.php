<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Casa de productie filme independenta </title>
    <style>
        li {
            margin-bottom: 16px; /* Adjust spacing as needed */
        }
    </style>
</head>

<body>
    <div class="container">
        <h1> Descrierea Aplicatiei Web </h1>

        <br>
        <br>

        <ul>
            <li>
                <h2> Scopul aplicatiei </h2>
                <p> Aplicatia isi propune sa prezinte proiectele in curs de dezvoltare, <br>
                dar si cele finalizate, ale unei case de productie de film independente.<br>
                Utilizatorul va avea oportunitatea sa cunoasca viziunea companiei, componenta echipei de productie <br>
                si principalii actori de se afla in contract cu aceasta casa de productie. </p>

                <p> Utilizatorii vor putea lua parte la activitatea casei de productie astfel: </p>
                <ul> 
                    <li> Adaugare de noi proiecte (filme, scurtmetraje) - marcheaza inceputul productiei </li>
                    <li> Actualizarea stadiului de productie al proiectelor existente (in pre-productie, in productie, post-productie, finalizat)</li>   
                    <li> Adaugare de membri in echipa de productie si atribuirea de roluri specifice acestora </li>
                    <li> Adaugare de resurse financiare pentru derularea proiectelor </li>
                </ul>
            </li>
            <li>
                <h2> Roluri valabile la accesarea aplicatiei </h2>
                <ul>
                    <li> Administrator pagina web </li>
                    <li> Utilizator extern </li>
                    <li> Lider echipa de productie  </li>
                    <li> Staff echipa de productie (lumina, sunet, video) </li>
                    <li> Lider creativitate (viziune artistica) </li>
                    <li> Staff echipa creativitate (scenografie, vizual) </li>
                    <li> Lider finante </li>
                </ul>
            </li>

            <li>
                <h2> Entitati (se omit tabelele asociative, cat si atributele de tip "created_at") </h2>
                <ul>
                    <li> <strong> Proiect </strong> - film sau scurtmetraj produs de casa de productie
                        <ul>
                            <li>id</li>
                            <li>tip: film, scurtmetraj</li>
                            <li>titlu</li>
                            <li>buget: bugetul aproximat la inceputul productiei</li>
                            <li>descriere: viziunea artistica a proiectului</li>
                            <li>durata_derulare: INT NULL (în minute)</li>
                            <li>poster_url</li>
                            <li>contribuitor</li>
                        </ul>           
                    </li>
                    <li> <strong> Status Proiect </strong> - stadiul de productie al unui proiect
                        <ul>
                            <li>id</li>
                            <li>nume: statusul curent (finalizat, in productie, productia incepe in curand)</li>
                            <li>data start</li>
                            <li>data finalizare</li>
                            <li>nota aditionala</li>
                        </ul>           
                    </li>
                    <li> <strong> Echipa </strong> - echipa de productie sau creativa asociata unui proiect
                        <ul>
                            <li>id</li>
                            <li>tip: echipele asociate unui proiect pot fi de productie sau creative</li>
                            <li>descriere: scurta descriere a echipei</li>
                            <li>numar_membri</li>
                        </ul>
                    </li>

                    <li> <strong> Rol </strong> - roluri ce pot fi atribuite utilizatorilor
                        <ul>
                            <li>id</li>
                            <li>nume</li>
                            <li>descriere: ce proprietati are acest rol</li>
                        </ul>
                    </li>


                    <li> <strong> Utilizator </strong> - utilizator al aplicatiei web
                        <ul>
                            <li>id</li>
                            <li>email</li>
                            <li>hash_parola</li>
                            <li>prenume</li>
                            <li>nume_familie</li>
                            <li>username</li>
                            <li>bio</li>
                            <li>avatar_url</li>
                            <li>numar_telefon</li>
                        </ul>
                    </li>

                    <li> <strong> Watchlist Item </strong> - proiecte urmarite de utilizatori
                        <ul>
                            <li>id</li>
                            <li>added_at: data adaugarii filmului la lista de vizionare</li>
                        </ul>
                    </li>

                    <li> <strong> Raport Financiar </strong> - raport financiar asociat unui proiect
                        <ul>
                            <li>id</li>
                            <li>nume_raport</li>
                            <li>start: data de inceput a raportului</li>
                            <li>finalizare: data de final a raportului</li>
                            <li>suma_totala: suma totala raportata</li>
                            <li>status: poate fi "schita", "trimis catre aprobare" sau "aprobat"</li>
                        </ul>
                    </li>
                </li>
            </ul>

            <li>
                <h2> Relații între entități </h2>
                <ul>
                    <li> <strong>STATUS_PROIECT → PROIECT</strong> (One-to-Many)
                        <ul>
                            <li>Un status poate fi asociat cu mai multe proiecte</li>
                            <li>Un proiect are un singur status la un moment dat</li>
                            <li>Cheia străină: PROIECT.id_status → STATUS_PROIECT.id</li>
                        </ul>
                    </li>

                    <li> <strong>ECHIPA → USER</strong> (One-to-Many)
                        <ul>
                            <li>O echipă poate avea mai mulți utilizatori</li>
                            <li>Un utilizator poate face parte dintr-o singură echipă</li>
                            <li>Cheia străină: USER.id_echipa → ECHIPA.id</li>
                        </ul>
                    </li>

                    <li> <strong>USER ↔ ROL</strong> (Many-to-Many prin ROL_USER)
                        <ul>
                            <li>Un utilizator poate avea mai multe roluri</li>
                            <li>Un rol poate fi atribuit mai multor utilizatori</li>
                            <li>Tabel asociativ: ROL_USER cu atribute suplimentare (assigned_at, expires_at)</li>
                        </ul>
                    </li>

                    <li> <strong>USER → PROIECT</strong> (One-to-Many - Contribuitor)
                        <ul>
                            <li>Un utilizator poate fi contribuitor la mai multe proiecte</li>
                            <li>Un proiect are un singur contribuitor principal</li>
                            <li>Cheia străină: PROIECT.contribuitor → USER.id</li>
                        </ul>
                    </li>

                    <li> <strong>PROIECT ↔ USER</strong> (Many-to-Many prin MEMBRU_PROIECT)
                        <ul>
                            <li>Un proiect poate avea mai mulți membri în echipă</li>
                            <li>Un utilizator poate lucra la mai multe proiecte</li>
                            <li>Tabel asociativ: MEMBRU_PROIECT cu atribute suplimentare (tip_echipa, assigned_at, expires_at)</li>
                        </ul>
                    </li>

                    <li> <strong>USER ↔ PROIECT</strong> (Many-to-Many prin WATCHLIST_ITEM)
                        <ul>
                            <li>Un utilizator poate urmări mai multe proiecte</li>
                            <li>Un proiect poate fi urmărit de mai mulți utilizatori</li>
                            <li>Tabel asociativ: WATCHLIST_ITEM cu atribut suplimentar (added_at)</li>
                        </ul>
                    </li>

                    <li> <strong>PROIECT → RAPORT_FINANCIAR</strong> (One-to-Many)
                        <ul>
                            <li>Un proiect poate avea mai multe rapoarte financiare</li>
                            <li>Un raport financiar aparține unui singur proiect</li>
                            <li>Cheia străină: RAPORT_FINANCIAR.id_proiect → PROIECT.id</li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li> 
                <h2> Descrierea bazei de date </h2>
                <p> Baza de date modelează activitatea unei case de producţie de film: proiecte (filme sau scurtmetraje), echipe, utilizatori, roluri, liste personale de vizionare şi rapoarte financiare. </p>

                <p>
                     Fiecare tip de entitate are un identificator unic şi atributele care îi definesc semnificaţia (de exemplu, un PROIECT are titlu, tip, buget, descriere, durată şi un poster; un USER are email, nume, avatar şi opţional apartenenţă la o ECHIPA).
                </p>

                <p>   
                    Relaţiile principale asigură contextul de business. Un STATUS_PROIECT (de ex. „în producţie”, „finalizat”) poate fi legat la multe proiecte; în orice moment un proiect are un singur status (cheia externă PROIECT.id_status indică rândul din STATUS_PROIECT). Astfel poţi filtra proiectele după stadiu sau poţi vedea istoricul planificat prin datele data_start / data_finalizare din STATUS_PROIECT.
                </p>

                <p>
                    Echipele (ECHIPA) grupează utilizatorii: o echipă poate avea mai mulţi USER, iar fiecare utilizator este membru al unei singure echipe (relaţie one-to-many). Acest lucru permite organizarea personalului pe structuri permanente (de exemplu „echipa de producţie” vs „echipa creativă”) şi simplifică atribuirea responsabilităţilor pe proiecte.
                </p>

                <p>
                    Rolurile (ROL) sunt independente şi se aplică utilizatorilor printr-o tabelă asociativă (ROL_USER). Modelul many-to-many înseamnă că un utilizator poate primi mai multe roluri (de exemplu „lider producţie” şi „utilizator extern” - daca acesta doreste mai multe tipuri de profil) şi un rol poate fi dat multor utilizatori. Tabela asociativă păstrează şi metadatele atribuirii (de exemplu assigned_at, expires_at), astfel încât poţi avea roluri temporare sau istorice.
                </p>

                <p>
                    Legătura dintre utilizatori şi proiecte este dublă: pe de o parte fiecare proiect are un contribuitor (un user care a iniţiat sau coordonează proiectul), iar pe de altă parte relaţia de colaborare efectivă este many-to-many prin MEMBRU_PROIECT — un proiect are mulţi membri şi un utilizator poate lucra la multe proiecte. MEMBRU_PROIECT poate stoca tipul echipei pentru acel user în cadrul proiectului (production/creative) şi perioadele de alocare (assigned_at, expires_at).
                </p>

                <p>
                    Pentru experienţa utilizatorilor, fiecare USER are o listă de vizionare implementată prin WATCHLIST_ITEM: aceasta leagă useri şi proiecte şi reţine când a fost adăugat (added_at). Astfel, un utilizator poate urmări orice număr de proiecte, iar un proiect poate apărea în listele multor utilizatori. Datorită constrângerilor, nu poţi adăuga duplicate (acelaşi proiect de două ori pentru acelaşi user) şi, dacă un user sau proiect este şters, elementele aferente din watchlist sunt curăţate automat.
                </p>

                <p>
                    Partea financiară este acoperită de RAPORT_FINANCIAR: fiecare raport aparţine unui singur proiect (one-to-many) şi conţine perioadă, sumă totală şi statusul raportului (schiţă, trimis, aprobat). suma_totala este stocată în DECIMAL(14,2) pentru a păstra precizia monedelor. Prin aceste rapoarte se urmăresc cheltuielile şi bugetul proiectului pe intervale de timp.
                </p>

                <p>
                    La nivel de integritate, baza foloseşte chei primare şi chei externe pentru a lega entităţile: FK-urile păstrează consistenţa datelor (de exemplu PROIECT.id_status trebuie să existe în STATUS_PROIECT, MEMBRU_PROIECT.id_user trebuie să existe în USER). Regulile de propagare (ON DELETE CASCADE, ON UPDATE CASCADE sau ON DELETE SET NULL după caz) stabilesc comportamentul la ştergeri sau actualizări: de exemplu, dacă un proiect e şters, rapoartele şi înregistrările din watchlist asociate vor fi eliminate automat.
                </p>
                <p>
                    Operaţiunile uzuale pe această bază reflectă exact fluxurile unui studio: crearea unui proiect (legat la un status şi la un contribuitor), atribuirea de membri şi roluri pe proiect, actualizarea stadiului pe măsură ce producţia evoluează,
                        adăugarea proiectelor în watchlist de către utilizatori şi generarea/validarea rapoartelor financiare care marchează cheltuielile şi bugetul. Datorită structurii relationale şi a metadatelor temporale (assigned_at, added_at, start/finalizare), poţi rula rapoarte istorice sau filtra rapid starea curentă a resurselor şi a costurilor.
                </p>
            </li>

            <li>
                <h2> Diagrama bazei de date </h2>
                <img src="../../public/diagrama_productie_filme.png" alt="Diagrama baza de date" style="max-width:80%; height:auto;">
            </li>

            <li> 
                <h2> Crearea tabelelor în baza de date </h2>
                <pre> 
                    SET NAMES utf8mb4;
                    SET sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER';

                    CREATE TABLE STATUS_PROIECT (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nume VARCHAR(100) NOT NULL,
                    data_start DATETIME NOT NULL,
                    data_finalizare DATETIME,
                    nota_aditionala TEXT
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


                    CREATE TABLE ECHIPA (
                    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
                    tip ENUM('Productie', 'Creativa') NOT NULL,
                    descriere TEXT,
                    numar_membri INT
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


                    CREATE TABLE ROL (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nume VARCHAR(150) NOT NULL,
                    descriere TEXT
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

                    DROP TABLE USER;

                    CREATE TABLE USER (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    hash_parola VARCHAR(255) NULL,
                    prenume VARCHAR(200) NULL,
                    nume_familie VARCHAR(200) NULL,
                    username VARCHAR(150) NULL,
                    bio TEXT NULL,
                    avatar_url TEXT NULL,
                    numar_telefon VARCHAR(40) NULL,
                    id_echipa CHAR(36),
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    deleted_at DATETIME NULL,
                    CONSTRAINT fk_USER_ECHIPA FOREIGN KEY (id_echipa) REFERENCES ECHIPA(id) ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    CREATE INDEX idx_users_team ON USER(id_echipa);


                    CREATE TABLE ROL_USER (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    id_user INT NOT NULL,
                    id_rol INT NOT NULL,
                    assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    expires_at DATETIME NULL,
                    CONSTRAINT fk_ur_user FOREIGN KEY (id_user) REFERENCES USER(id)  ON UPDATE CASCADE,
                    CONSTRAINT fk_ur_rol FOREIGN KEY (id_rol) REFERENCES ROL(id) ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    CREATE INDEX idx_user_roles_user ON ROL_USER(id_user);
                    CREATE INDEX idx_user_roles_role ON ROL_USER(id_rol);

                    ALTER TABLE PROIECT
                    ADD COLUMN buget DECIMAL(14,2) DEFAULT 0;

                    CREATE TABLE PROIECT (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    tip VARCHAR(255) NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    buget DECIMAL(14,2) DEFAULT 0,
                    descriere TEXT NULL,
                    id_status INT NOT NULL,
                    durata_derulare INT NULL, -- in minute
                    poster_url TEXT NULL,
                    contribuitor INT NOT NULL ,
                    CONSTRAINT fk_proiect_status FOREIGN KEY (id_status) REFERENCES STATUS_PROIECT(id) ON UPDATE CASCADE,
                    CONSTRAINT fk_proiect_contributie FOREIGN KEY (contribuitor) REFERENCES USER(id) ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


                    CREATE TABLE MEMBRU_PROIECT (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    id_proiect INT NOT NULL,
                    id_user INT NOT NULL,
                    tip_echipa VARCHAR(50) NULL,
                    assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    expires_at DATETIME NULL,
                    CONSTRAINT uq_proiect_user_rol UNIQUE (id_proiect, id_user),
                    CONSTRAINT fk_pm_proiect FOREIGN KEY (id_proiect) REFERENCES PROIECT(id) ON UPDATE CASCADE,
                    CONSTRAINT fk_pm_user FOREIGN KEY (id_user) REFERENCES USER(id) ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


                    CREATE TABLE WATCHLIST_ITEM (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    id_user INT NOT NULL ,
                    id_proiect INT NOT NULL ,
                    added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

                    CONSTRAINT fk_watchlist_user FOREIGN KEY (id_user) REFERENCES USER(id)
                        ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT fk_watchlist_project FOREIGN KEY (id_proiect) REFERENCES PROIECT(id)
                        ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

                    CREATE TABLE  RAPORT_FINANCIAR(
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    id_proiect INT NOT NULL ,
                    nume_raport VARCHAR(255) NOT NULL,
                    start DATE NULL,
                    finalizare DATE NULL,
                    suma_totala DECIMAL(14,2) DEFAULT 0,
                    status ENUM('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    CONSTRAINT fk_fr_proiect FOREIGN KEY (id_proiect) REFERENCES PROIECT(id) ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

                </pre>
            </li> 

            <li>
                <h2> Diagrama UML </h2>
                <img src="../../public/diagrama_uml_finala.png" alt="Diagrama uml" style="max-width:100%; height:auto;">
            </li>
        </ul>


