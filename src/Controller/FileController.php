<?php
namespace App\Controller;
use App\Entity\Document;
use App\Entity\Preview;
use App\Helper\UploadHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileController extends AbstractController
{

    public function getDocs() {
        /**
         * @var Document $document
         */
        $documents = $this->getDoctrine()->getRepository(Document::class)->findAll();
        $response = [];
        foreach ($documents as $document) {
            $response[] = $document->toArray();
        }
        return new JsonResponse($response);
    }

    public function getDoc($document_id) {
        /**
         * @var Document $document
         */
        $document = $this->getDoctrine()->getRepository(Document::class)->find($document_id);
        return new JsonResponse($document->toArray());
    }

    public function createDoc() {
        $em         = $this->getDoctrine()->getManager();

        $uploader = new UploadHelper();

        $pdfPath    = $uploader->uploadPDF($_FILES['file']);
        $thumbnails = $uploader->generatePDFThumbnails($pdfPath);

        $file_name  = $uploader->getFilename($pdfPath);
        $document   = new Document();
        $document->setName($file_name);
        $em->persist($document);


        foreach($thumbnails as $thumbnail) {
            $newPreview = new Preview();
            $newPreview->setName($thumbnail)->setDocument($document);
            $document->addPreview($newPreview);
            $em->persist($newPreview);
        }

        $em->flush();
        return new JsonResponse($document->toArray());
    }

    public function deleteDoc($document_id) {
        /**
         * @var Document $document
         */
        $em         = $this->getDoctrine()->getManager();
        $document   = $em->getRepository(Document::class)->find($document_id);

        $uploader = new UploadHelper();
        $uploader->removePDF($document->getName());
        $uploader->removeThumbnails($document->getName());

        $em->remove($document);
        $em->flush();
        return new JsonResponse(['id' => $document_id]);
    }

    public function getDocAttachment($document_id) {
        $em         = $this->getDoctrine()->getManager();
        $document   = $em->getRepository(Document::class)->find($document_id);
        if (!$document) {
            throw new \Exception('Document not found',404);
        }

        $uploader   = new UploadHelper();
        $path       = $uploader->getPDFFilePath($document->getName());

        if (!file_exists($path)) {
            throw new \Exception('File not found',404);
        }
        $response = new Response(file_get_contents($path));

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $document->getName()
        );

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        // Dispatch request
        return $response;
    }

    public function getDocAttachmentPreviews($document_id) {
        /**
         * @var Document $document
         * @var Preview $preview
         */
        $em         = $this->getDoctrine()->getManager();
        $document   = $em->getRepository(Document::class)->find($document_id);

        $response   = [];
        foreach ($document->getPreviews() as $preview) {
            $response[] = $preview->toArray();
        }
        return new JsonResponse($response);
    }

    public function getDocAttachmentPreview($document_id, $preview_id) {
        /**
         * @var Preview $preview
         */
        $em         = $this->getDoctrine()->getManager();
        $preview    = $em->getRepository(Preview::class)->find($preview_id);

        $uploader   = new UploadHelper();
        $path       = $uploader->getThumbnailFilePath($preview->getDocument()->getName(), $preview->getName());

        $response = new Response(file_get_contents($path));

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $preview->getName()
        );

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        // Dispatch request
        return $response;
    }

    public function addDocAttachmentPreview($document_id) {
        /**
         * @var Document $document
         * @var Preview $preview
         */
        $em         = $this->getDoctrine()->getManager();
        $document   = $em->getRepository(Document::class)->find($document_id);

        $uploader   = new UploadHelper();

        $filePath   = $uploader->addThumbnail($document->getName(), $_FILES['file']);
        $filename   = $uploader->getFilename($filePath);

        $preview    = new Preview();
        $preview->setDocument($document)->setName($filename);

        $document->addPreview($preview);
        $em->persist($preview);
        $em->flush();
        return new JsonResponse($preview->toArray());
    }

    public function deleteDocAttachmentPreview($document_id, $preview_id) {
        /**
         * @var Preview $preview
         */
        $em         = $this->getDoctrine()->getManager();
        $preview    = $em->getRepository(Preview::class)->find($preview_id);

        $uploader   = new UploadHelper();
        $uploader->removeThumbnail($preview->getDocument()->getName(), $preview->getName());

        $em->remove($preview);
        $em->flush();
        return new JsonResponse(['id' => $preview_id]);
    }
}