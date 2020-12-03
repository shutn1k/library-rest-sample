<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201203061852 extends AbstractMigration {

    public function getDescription(): string {

        return 'DDL';
    }

    public function up(Schema $schema): void {

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lr_author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lr_book (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lr_book_author (book_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_1C2B09D816A2B381 (book_id), INDEX IDX_1C2B09D8F675F31B (author_id), PRIMARY KEY(book_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lr_book_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_1B4132E52C2AC5D3 (translatable_id), UNIQUE INDEX lr_book_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lr_book_author ADD CONSTRAINT FK_1C2B09D816A2B381 FOREIGN KEY (book_id) REFERENCES lr_book (id)');
        $this->addSql('ALTER TABLE lr_book_author ADD CONSTRAINT FK_1C2B09D8F675F31B FOREIGN KEY (author_id) REFERENCES lr_author (id)');
        $this->addSql('ALTER TABLE lr_book_translation ADD CONSTRAINT FK_1B4132E52C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES lr_book (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void {

        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lr_book_author DROP FOREIGN KEY FK_1C2B09D8F675F31B');
        $this->addSql('ALTER TABLE lr_book_author DROP FOREIGN KEY FK_1C2B09D816A2B381');
        $this->addSql('ALTER TABLE lr_book_translation DROP FOREIGN KEY FK_1B4132E52C2AC5D3');
        $this->addSql('DROP TABLE lr_author');
        $this->addSql('DROP TABLE lr_book');
        $this->addSql('DROP TABLE lr_book_author');
        $this->addSql('DROP TABLE lr_book_translation');
    }
}
