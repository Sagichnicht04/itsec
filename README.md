Idee war folgende:
Erklären der ganzen Schritte zum speichern von User-Passwörtern in einer MariaDB

Alles beginnt mit einem Sign Up screen (natürlich mit HTTPS) wo der User sein password eingibt und auf sign up klickt.
Im nächsten Screen sieht man dann alle folge schritte. Data breach check, HTTP-Übertragung, Peppern, salten, hashen (hier werden verschiedene Algorithmen erklärt) und speichern. Als letztes wechselt man noch zu PHP-My Admin und schaut sich an wie das passwort dort abgespeichert ist.
Success!!

Wenn Zeit ist kann man noch erklären wie Argon2Id aussieht:
1. argon2id – Algorithm type
2. v=19 – Version
3. m=65536 – Memory cost 65,536 kibibytes (KiB) = 64 MiB memory used during hashing.
4. t=4 – Time cost
5. p=3 – Parallelism = This is the number of lanes/threads Argon2 uses.
6. YVhHYzRQY1UvV2ttdHVoaw – Salt (Base64-encoded) = encoded in Base64.
7. fPchrib7aWVtlhUFH0px4H7nBJL+NeTOVEp5p2ppIiI – Hash digest
