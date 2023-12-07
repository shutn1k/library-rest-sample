<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231212114959 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Initial migration.';
    }

    /**
     * @inheritDoc
     */
    public function up(Schema $schema): void
    {

        $this->addSql(<<<SQL
CREATE TABLE ext_translations (
    id INT AUTO_INCREMENT NOT NULL, 
    locale VARCHAR(8) NOT NULL, 
    object_class VARCHAR(191) NOT NULL, 
    field VARCHAR(32) NOT NULL, 
    foreign_key VARCHAR(64) NOT NULL, 
    content LONGTEXT DEFAULT NULL, 
    INDEX translations_lookup_idx (locale, object_class, foreign_key), 
    INDEX general_translations_lookup_idx (object_class, foreign_key), 
    UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), 
    PRIMARY KEY(id)
) 
DEFAULT CHARACTER SET utf8mb4 
COLLATE `utf8mb4_unicode_ci` 
ENGINE = InnoDB 
ROW_FORMAT = DYNAMIC
SQL
        );
        $this->addSql(<<<SQL
CREATE TABLE l_author (
    id INT AUTO_INCREMENT NOT NULL, 
    name VARCHAR(255) NOT NULL, 
    PRIMARY KEY(id)
) 
DEFAULT CHARACTER SET utf8mb4 
COLLATE `utf8mb4_unicode_ci` 
ENGINE = InnoDB
SQL
        );
        $this->addSql(<<<SQL
CREATE TABLE l_book (
    id INT AUTO_INCREMENT NOT NULL, 
    title VARCHAR(255) NOT NULL, 
    INDEX title_idx (title), 
    PRIMARY KEY(id)
) 
DEFAULT CHARACTER SET utf8mb4 
COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL
        );
        $this->addSql(<<<SQL
CREATE TABLE l_book_author (
    book_id INT NOT NULL, 
    author_id INT NOT NULL, 
    INDEX IDX_2A2D82F316A2B381 (book_id), 
    INDEX IDX_2A2D82F3F675F31B (author_id), 
    PRIMARY KEY(book_id, author_id)
) 
DEFAULT CHARACTER SET utf8mb4 
COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL
        );
        $this->addSql(<<<SQL
ALTER TABLE l_book_author 
    ADD CONSTRAINT FK_2A2D82F316A2B381 FOREIGN KEY (book_id)REFERENCES l_book (id) ON DELETE CASCADE
SQL
        );
        $this->addSql(<<<SQL
ALTER TABLE l_book_author 
    ADD CONSTRAINT FK_2A2D82F3F675F31B FOREIGN KEY (author_id) REFERENCES l_author (id) ON DELETE CASCADE
SQL
        );
    }

    /**
     * @inheritDoc
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE l_book_author DROP FOREIGN KEY FK_2A2D82F316A2B381');
        $this->addSql('ALTER TABLE l_book_author DROP FOREIGN KEY FK_2A2D82F3F675F31B');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE l_author');
        $this->addSql('DROP TABLE l_book');
        $this->addSql('DROP TABLE l_book_author');
    }
}
