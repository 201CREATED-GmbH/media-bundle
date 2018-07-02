c201_media:
    entities:
        "C201ProductCatalogBundle:Product":
            upload_path: "%kernel.root_dir%/../web/uploads/products/{filesystemize(entity.slug, 2)}"
            medias:
                image1:
                    name: "image1_{hash(5)}.{file.extension}"
                    default: "%kernel.root_dir%/../web/images/default-image.jpg"
                    constraints:
                        \Symfony\Component\Validator\Constraints\Image:
                            mimeTypes: image/jpeg
                            minWidth:  300
                            minHeight: 300
                pdfProspect:
                    name: "{entity.slug}_{hash(5)}.{file.extension}"
                    constraints:
                        \Symfony\Component\Validator\Constraints\File:
                            mimeTypes: application/pdf