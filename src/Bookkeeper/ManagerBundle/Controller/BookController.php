<?php

namespace Bookkeeper\ManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Bookkeeper\ManagerBundle\Entity\Book;
use Bookkeeper\ManagerBundle\Form\BookType;

class BookController extends Controller {

	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$books = $em->getRepository('BookkeeperManagerBundle:Book')->findAll();
		return $this->render('BookkeeperManagerBundle:Book:index.html.twig', array(
			'books'=>$books
		));
	}

	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$book = $em->getRepository('BookkeeperManagerBundle:Book')->find($id);

		$delete_form = $this->createFormBuilder()
			->setAction($this->generateUrl('book_delete', array('id'=>$id)))
			->setMethod('DELETE')
			->add('submit', 'submit', array('label'=>'Delete Book'))
			->getForm();

		return $this->render('BookkeeperManagerBundle:Book:show.html.twig', array(
			'book'=>$book,
			'delete_form'=>$delete_form->createView()
		));
	}

	public function newAction() {
		$book = new Book();

		$form = $this->createForm(new BookType(), $book, array(
			'action'=>$this->generateUrl('book_create'),
			'method'=>'POST'
		));

		$form->add('submit', 'submit', array('label'=>'Create Book'));

		return $this->render('BookkeeperManagerBundle:Book:new.html.twig', array(
			'form'=>$form->createView()
		));
	}

	public function createAction(Request $request) {
		$book = new Book();

		$form = $this->createForm(new BookType(), $book, array(
			'action'=>$this->generateUrl('book_create'),
			'method'=>'POST'
		));

		$form->add('submit', 'submit', array('label'=>'Create Book'));

		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($book);
			$em->flush();

			$this->get('session')->getFlashBag()->add('msg', 'Your book has been created!');

			return $this->redirect($this->generateUrl('book_show', array('id'=>$book->getId())));
		}

		$this->get('session')->getFlashBag()->add('msg', 'Something went wrong!');

		return $this->render('BookkeeperManagerBundle:Book:new.html.twig', array(
			'form'=>$form->createView()
		));
	}

	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$book = $em->getRepository('BookkeeperManagerBundle:Book')->find($id);

		$form = $this->createForm(new BookType(), $book, array(
			'action'=>$this->generateUrl('book_update', array('id'=>$book->getId())),
			'method'=>'PUT'
		));
		$form->add('submit', 'submit', array('label'=>'Update Book'));

		return $this->render('BookkeeperManagerBundle:Book:edit.html.twig', array(
			'form'=>$form->createView()
		));
	}

	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$book = $em->getRepository('BookkeeperManagerBundle:Book')->find($id);

		$form = $this->createForm(new BookType(), $book, array(
			'action'=>$this->generateUrl('book_update', array('id'=>$book->getId())),
			'method'=>'PUT'
		));
		$form->add('submit', 'submit', array('label'=>'Update Book'));

		$form->handleRequest($request);

		if ($form->isValid()) {
			$em->flush();

			$this->get('session')->getFlashBag()->add('msg', 'Your book has been updated!');

			return $this->redirect($this->generateUrl('book_show', array('id'=>$id)));
		}

		return $this->render('BookkeeperManagerBundle:Book:edit.html.twig', array(
			'form'=>$form->createView()
		));
	}

	public function deleteAction(Request $request, $id) {
		$delete_form = $this->createFormBuilder()
			->setAction($this->generateUrl('book_delete', array('id'=>$id)))
			->setMethod('DELETE')
			->add('submit', 'submit', array('label'=>'Delete Book'))
			->getForm();

		$delete_form->handleRequest($request);

		if ($delete_form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$book = $em->getRepository('BookkeeperManagerBundle:Book')->find($id);
			$em->remove($book);
			$em->flush();
		}

		$this->get('session')->getFlashBag()->add('msg', 'Your book has been deleted!');

		return $this->redirect($this->generateUrl('book'));
	}	
}