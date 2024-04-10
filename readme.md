# Projet Symfony 7 - Photo Application

## 1. Diagramme entité-relation

```mermaid
erDiagram
    customer {
        int id PK
        string(255) name
        string(255) email
        int age
        string(255) city
        date created_at
    }
    photo {
        int id PK
        string(255) title
        string description
        string(255) url
        float price
        json meta_info
        string slug
        date created_at
        date modified_at
    }

    order {
        int id PK
        int customer_id FK
        date created_at
    }

    order_item {
        int id PK
        int order_id FK
        int photo_id FK
        int quantity FK
        float price
    }

    tag {
        int id PK
        string(255) name
        string(255) slug
    }

    photo_tag {
        int tag_id PK, FK
        int photo_id PK, FK
    }

    customer ||--o{ order : has
    order ||--o{ order_item : contains
    photo ||--o{ order_item : contains
    photo ||--o{ photo_tag : has
    tag ||--o{ photo_tag : contains
```
