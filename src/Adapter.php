<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP;

use FireHub\Kernel\Lifecycle\Engine;
use FireHub\Application\Application;
use FireHub\Kernel\Lifecycle\Exception\ {
    InvalidLifecycleStateException, LifecycleExecutionException
};
use FireHub\Adapter\HTTP\Exception\ {
    HttpAdapterDisposalException, HttpAdapterInitializationException, InvalidHttpAdapterStateException
};
use Closure;

/**
 * ### Manages the HTTP adapter lifecycle
 *
 * Coordinates HTTP adapter initialization and disposal while maintaining a lifecycle dependency on the owning
 * application.
 * @since 1.0.0
 */
final readonly class Adapter {

    /**
     * ### Lifecycle engine
     * @since 1.0.0
     *
     * @var \FireHub\Kernel\Lifecycle\Engine<\FireHub\Adapter\HTTP\State>
     */
    private Engine $lifecycle;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Application\Application::dependencyBoundary() To create a dependency boundary for the application.
     *
     * @param \FireHub\Application\Application $application <p>
     * The application that owns the HTTP adapter.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Application $application
    ) {

        $this->lifecycle = new Engine(
            State::CREATED,
            $application->dependencyBoundary()
        );

    }

    /**
     * ### Registers an HTTP adapter initializer
     *
     * Registers an initializer to be executed during the HTTP adapter initialization lifecycle.
     *
     * Initializers are executed in their configured execution order and receive the HTTP adapter instance, allowing
     * them to prepare HTTP-specific components and resources before the adapter becomes operational.
     *
     * Registration is permitted only before the adapter lifecycle has started. Once initialization begins, no
     * additional initializers can be registered.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Adapter;
     *
     * $adapter = new Adapter($frontend);
     *
     * $adapter->registerInitializer(new ClassThatImplementsInitializer);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Kernel\Lifecycle\Engine::registerStartup() To register the initialization callback.
     * @uses \FireHub\Adapter\HTTP\Initializer::initialize() To initialize the HTTP adapter component.
     *
     * @param \FireHub\Adapter\HTTP\Initializer $initializer <p>
     * Initializer to register.
     * </p>
     * @param int $order [optional] <p>
     * Execution order of the initializer.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidHttpAdapterStateException If initializer registration is not
     * permitted in the current adapter lifecycle state.
     *
     * @return void
     */
    public function registerInitializer (Initializer $initializer, int $order = 0):void {

        try {

            $this->lifecycle->registerStartup(
                function () use ($initializer):void {

                    $initializer->initialize($this);

                },
                $order
            );

        } catch (InvalidLifecycleStateException $exception) {

            throw new InvalidHttpAdapterStateException(
                'Cannot register an initializer in the current adapter state.',
                previous: $exception
            );

        }

    }

    /**
     * ### Registers an HTTP adapter disposer
     *
     * Registers a disposer to be executed during the HTTP adapter disposal lifecycle.
     *
     * Disposers are executed in their configured execution order and receive the HTTP adapter instance, allowing
     * them to release HTTP-specific resources and perform cleanup before the adapter completes its disposal.
     *
     * Registration is permitted only before the adapter lifecycle has started. Once initialization begins, no
     * additional disposers can be registered.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Adapter;
     *
     * $adapter = new Adapter($frontend);
     *
     * $adapter->registerDisposer(new ClassThatImplementsDisposer);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Kernel\Lifecycle\Engine::registerShutdown() To register the disposal callback.
     * @uses \FireHub\Adapter\HTTP\Disposer::dispose() To dispose of the HTTP adapter component.
     *
     * @param \FireHub\Adapter\HTTP\Disposer $disposer <p>
     * Disposer to register.
     * </p>
     * @param int $order [optional] <p>
     * Execution order of the disposer.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidHttpAdapterStateException If disposer registration is not
     * permitted in the current adapter lifecycle state.
     *
     * @return void
     */
    public function registerDisposer (Disposer $disposer, int $order = 0):void {

        try {

            $this->lifecycle->registerShutdown(
                function () use ($disposer):void {

                    $disposer->dispose($this);

                },
                $order
            );

        } catch (InvalidLifecycleStateException $exception) {

            throw new InvalidHttpAdapterStateException(
                'Cannot register a disposer in the current adapter state.',
                previous: $exception
            );

        }

    }

    /**
     * ### Initializes the HTTP adapter
     *
     * Starts the HTTP adapter initialization lifecycle and executes all registered initializers in their configured
     * execution order.
     *
     * Initialization requires the associated application to be initialized and available. The adapter acquires a
     * lifecycle dependency on the application, preventing the application from being disposed of while the adapter
     * remains active.
     *
     * During initialization, the adapter transitions from CREATED to INITIALIZING. Successful initialization
     * transitions the adapter to INITIALIZED, making it available for HTTP request processing.
     *
     * If initialization fails, the adapter transitions to INITIALIZATION_FAILED and preserves the acquired
     * application dependency until the adapter is disposed of.
     *
     * Initialization can only be performed once and is permitted exclusively from the CREATED state.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Adapter;
     *
     * $adapter = new Adapter($frontend);
     *
     * $adapter->initialize();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Kernel\Lifecycle\Engine::state() To get the current state.
     * @uses \FireHub\Kernel\Lifecycle\Engine::execute() To manage lifecycle state transitions.
     * @uses \FireHub\Kernel\Lifecycle\Engine::start() To execute registered initializers.
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidHttpAdapterStateException If initialization is not allowed.
     * @throws \FireHub\Adapter\HTTP\Exception\HttpAdapterInitializationException If an initializer fails.
     *
     * @return void
     */
    public function initialize ():void {

        if ($this->state() === State::INITIALIZED) return;

        try {

            $this->lifecycle->execute(
                callback: fn() => $this->lifecycle->start(),
                from: [State::CREATED],
                success: State::INITIALIZED,
                failure: State::INITIALIZATION_FAILED,
                entering: State::INITIALIZING,
                acquire_parent: true
            );

        } catch (InvalidLifecycleStateException $exception) {

            throw new InvalidHttpAdapterStateException(
                'Cannot initialize the application in the current state.',
                previous: $exception
            );

        } catch (LifecycleExecutionException $exception) {

            throw new HttpAdapterInitializationException(
                previous: $exception->getPrevious() ?? $exception
            );

        }

    }

    /**
     * ### Disposes of the HTTP adapter
     *
     * Starts the HTTP adapter disposal lifecycle and executes all registered disposers in their configured execution
     * order.
     *
     * Disposal is permitted when the adapter has not yet been initialized, has completed initialization, or has
     * encountered an initialization failure.
     *
     * During disposal, the adapter transitions to DISPOSING and releases HTTP-specific resources managed throughout
     * its lifecycle.
     *
     * Successful disposal transitions the adapter to DISPOSED and releases the lifecycle dependency acquired from
     * the associated application, allowing the application to proceed with its own disposal.
     *
     * Disposal requires that no active lifecycle dependents remain attached to the adapter. If disposal fails, the
     * adapter transitions to DISPOSAL_FAILED.
     *
     * Once the adapter was disposed, the HTTP adapter cannot be initialized or disposed again.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Adapter;
     *
     * $adapter = new Adapter($frontend);
     *
     * $adapter->dispose();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Kernel\Lifecycle\Engine::state() To get the current state.
     * @uses \FireHub\Kernel\Lifecycle\Engine::execute() To execute a callback.
     * @uses \FireHub\Kernel\Lifecycle\Engine::stop() To execute registered disposers.
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidHttpAdapterStateException If disposal is not allowed.
     * @throws \FireHub\Adapter\HTTP\Exception\HttpAdapterDisposalException If a disposer fails.
     *
     * @return void
     */
    public function dispose ():void {

        if ($this->state() === State::DISPOSED) return;

        try {

            $this->lifecycle->execute(
                callback: fn() => $this->lifecycle->stop(),
                from: [
                    State::CREATED,
                    State::INITIALIZED,
                    State::INITIALIZATION_FAILED
                ],
                success: State::DISPOSED,
                failure: State::DISPOSAL_FAILED,
                entering: State::DISPOSING,
                release_parent: true,
                require_no_dependents: true
            );

        } catch (InvalidLifecycleStateException $exception) {

            throw new InvalidHttpAdapterStateException(
                'Cannot dispose the application in the current state.',
                previous: $exception
            );

        } catch (LifecycleExecutionException $exception) {

            throw new HttpAdapterDisposalException(
                previous: $exception->getPrevious() ?? $exception
            );

        }

    }

    /**
     * ### Gets the current HTTP adapter lifecycle state
     *
     * Returns the HTTP adapter's own lifecycle state directly from its lifecycle engine.
     *
     * No conversion from kernel states is performed.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Adapter;
     *
     * $adapter = new Adapter($frontend);
     *
     * $adapter->state();
     *
     * // FireHub\Adapter\HTTP\State::CREATED
     *
     * $adapter->initialize();
     * $adapter->state();
     *
     * // FireHub\Adapter\HTTP\State::INITIALIZED
     *
     * $adapter->dispose();
     * $adapter->state();
     *
     * // FireHub\Adapter\HTTP\State::DISPOSED
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Kernel\Lifecycle\Engine::state() To get the current state.
     *
     * @return \FireHub\Adapter\HTTP\State Current HTTP adapter lifecycle state.
     */
    public function state ():State {

        return $this->lifecycle->state();

    }

    /**
     * ### Gets the associated application
     *
     * Returns the application instance associated with the HTTP adapter.
     *
     * The application provides the execution environment and lifecycle dependency required by the adapter.
     *
     * Multiple HTTP adapters may reference the same application, while each adapter maintains its own independent
     * lifecycle.
     *
     * Retrieving the application does not initialize it, modify its lifecycle state, or acquire an additional
     * lifecycle dependency.
     * @since 1.0.0
     *
     * @return \FireHub\Application\Application Associated application instance.
     */
    public function application ():Application {

        return $this->application;

    }

    /**
     * ### Executes an HTTP adapter operation
     *
     * Executes a callback while the HTTP adapter is initialized and available.
     *
     * Acquires an HTTP adapter lifecycle dependency to prevent disposal during execution.
     *
     * The associated application creates and manages the dependency injection scope.
     *
     * The adapter lifecycle dependency is released after execution completes, including when an exception is thrown.
     * @since 1.0.0
     *
     * @uses \FireHub\Kernel\Lifecycle\Engine::dependencyBoundary() To acquire the adapter lifecycle dependency.
     * @uses \FireHub\Kernel\Lifecycle\Dependency\Boundary::acquire() To acquire the lifecycle dependency.
     * @uses \FireHub\Kernel\Lifecycle\Dependency\Lease::release() To release the adapter lifecycle dependency.
     * @uses \FireHub\Application\Application::execute() To execute the operation within an application scope.
     *
     * @param Closure(\FireHub\Application\ExecutionContext):mixed $callback <p>
     * Operation to execute.
     * </p>
     *
     * @throws \FireHub\Kernel\Lifecycle\Exception\InvalidLifecycleStateException If the adapter is not initialized.
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidHttpAdapterStateException If the adapter is not available.
     * @throws \FireHub\Application\Exception\InvalidApplicationStateException If the application is not initialized.
     *
     * @return mixed Operation result.
     */
    private function execute (Closure $callback):mixed {

        try {

            $lease = $this->lifecycle->dependencyBoundary(
                static fn (State $state):bool => $state === State::INITIALIZED
            )->acquire();

        } catch (InvalidLifecycleStateException $exception) {

            throw new InvalidHttpAdapterStateException(
                'Cannot execute an operation in the current HTTP adapter state.',
                previous: $exception
            );

        }

        try {

            return $this->application->execute($callback);

        } finally {

            $lease->release();

        }

    }

}